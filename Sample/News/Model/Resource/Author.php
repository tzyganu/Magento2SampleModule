<?php
namespace Sample\News\Model\Resource;

use \Magento\Framework\Model\Resource\Db\AbstractDb;
use \Magento\Framework\App\Resource;
use \Magento\Framework\Stdlib\DateTime\DateTime;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\Stdlib\DateTime as LibDateTime;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Store\Model\Store;


class Author extends AbstractDb
{
    /**
     * Store model
     *
     * @var \Magento\Store\Model\Store
     */
    protected $store = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * constructor
     *
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     */
    public function __construct(
        Resource $resource,
        DateTime $date,
        StoreManagerInterface $storeManager,
        LibDateTime $dateTime
    ) {
        parent::__construct($resource);
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sample_news_author', 'author_id');
    }

    /**
     * Process author data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(AbstractModel $object)
    {
        $condition = ['author_id = ?' => (int)$object->getId()];
        $this->_getWriteAdapter()->delete($this->getTable('sample_news_author_store'), $condition);
        return parent::_beforeDelete($object);
    }

    /**
     * before save callback
     *
     * @param AbstractModel|\Sample\News\Model\Author $object
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        foreach (['dob'] as $field) {
            $value = !$object->getData($field) ? null : $object->getData($field);
            $object->setData($field, $this->dateTime->formatDate($value));
        }
        $object->setUpdatedAt($this->date->gmtDate());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->date->gmtDate());
        }
        $urlKey = $object->getData('url_key');
        if ($urlKey == '') {
            $urlKey = $object->getName();
        }
        $urlKey = $object->formatUrlKey($urlKey);
        $object->setUrlKey($urlKey);
        $validKey = false;
        while (!$validKey) {
            if ($this->getIsUniqueAuthorToStores($object)) {
                $validKey = true;
            } else {
                $parts = explode('-', $urlKey);
                $last = $parts[count($parts) - 1];
                if (!is_numeric($last)){
                    $urlKey = $urlKey.'-1';
                } else {
                    $suffix = '-'.($last + 1);
                    unset($parts[count($parts) - 1]);
                    $urlKey = implode('-', $parts).$suffix;
                }
                $object->setData('url_key', $urlKey);
            }
        }
        return parent::_beforeSave($object);
    }

    /**
     * Assign author to store views
     *
     * @param AbstractModel|\Sample\News\Model\Author $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table = $this->getTable('sample_news_author_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = [
                'author_id = ?' => (int)$object->getId(),
                'store_id IN (?)' => $delete
            ];
            $this->_getWriteAdapter()->delete($table, $where);
        }
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'author_id' => (int)$object->getId(),
                    'store_id' => (int)$storeId
                ];
            }
            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }

    /**
     * Load an object using 'url_key' field if there's no field specified and value is not numeric
     *
     * @param AbstractModel|\Sample\News\Model\Author $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'url_key';
        }
        return parent::load($object, $value, $field);
    }

    /**
     * Perform operations after object load
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }
        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Sample\News\Model\Author $object
     * @return \Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [
                Store::DEFAULT_STORE_ID,
                (int)$object->getStoreId()
            ];
            $select->join(
                [
                    'sample_news_author_store' => $this->getTable('sample_news_author_store')
                ],
                $this->getMainTable() . '.author_id = sample_news_author_store.author_id',
                []
            )//TODO: check if is_active filter is needed
            ->where('is_active = ?', 1)
            ->where(
                'sample_news_author_store.store_id IN (?)',
                $storeIds
            )
            ->order('sample_news_author_store.store_id DESC')
            ->limit(1);
        }
        return $select;
    }

    /**
     * Retrieve load select with filter by url_key, store and activity
     *
     * @param string $urlKey
     * @param int|array $store
     * @param int $isActive
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadByUrlKeySelect($urlKey, $store, $isActive = null)
    {
        $select = $this->_getReadAdapter()
            ->select()
            ->from(['author' => $this->getMainTable()])
            ->join(
                ['author_store' => $this->getTable('sample_news_author_store')],
                'author.author_id = author_store.author_id',
                []
            )
            ->where(
                'author.url_key = ?',
                $urlKey
            )
            ->where(
                'author_store.store_id IN (?)',
                $store
            );
        if (!is_null($isActive)) {
            $select->where('author.is_active = ?', $isActive);
        }
        return $select;
    }


    /**
     * Check if author url_key exist
     * return author id if author exists
     *
     * @param string $urlKey
     * @param int $storeId
     * @return int
     */
    public function checkUrlKey($urlKey, $storeId)
    {
        $stores = [Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByUrlKeySelect($urlKey, $stores, 1);
        $select->reset(\Zend_Db_Select::COLUMNS)
            ->columns('author.author_id')
            ->order('author_store.store_id DESC')
            ->limit(1);
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves author name from DB by passed url key.
     *
     * @param string $urlKey
     * @return string|bool
     */
    public function getAuthorNameByUrlKey($urlKey)
    {
        $stores = [Store::DEFAULT_STORE_ID];
        if ($this->store) {
            $stores[] = (int)$this->getStore()->getId();
        }
        $select = $this->_getLoadByUrlKeySelect($urlKey, $stores);
        $select->reset(\Zend_Db_Select::COLUMNS)
            ->columns('author.name')
            ->order('author.store_id DESC')
            ->limit(1);
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves author name from DB by passed id.
     *
     * @param string $id
     * @return string|bool
     */
    public function getAuthorNameById($id)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('author_id = :author_id');
        $binds = ['author_id' => (int)$id];
        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Retrieves author url key from DB by passed id.
     *
     * @param int $id
     * @return string|bool
     */
    public function getAuthorUrlKeyById($id)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'url_key')
            ->where('author_id = :author_id');
        $binds = ['author_id' => (int)$id];
        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $authorId
     * @return array
     */
    public function lookupStoreIds($authorId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->getTable('sample_news_author_store'),
            'store_id'
        )
        ->where(
            'author_id = ?',
            (int)$authorId
        );
        return $adapter->fetchCol($select);
    }

    /**
     * Set store model
     *
     * @param Store $store
     * @return $this
     */
    public function setStore(Store $store)
    {
        $this->store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->storeManager->getStore($this->store);
    }

    /**
     * check if url key is unique
     *
     * @param AbstractModel|\Sample\News\Model\Author $object
     * @return bool
     */
    public function getIsUniqueAuthorToStores(AbstractModel $object)
    {
        if ($this->storeManager->hasSingleStore() || !$object->hasStores()) {
            $stores = [Store::DEFAULT_STORE_ID];
        } else {
            $stores = (array)$object->getData('stores');
        }
        $select = $this->_getLoadByUrlKeySelect($object->getData('url_key'), $stores);
        if ($object->getId()) {
            $select->where('author_store.author_id <> ?', $object->getId());
        }
        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }
        return true;
    }
}
