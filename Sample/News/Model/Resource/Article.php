<?php
namespace Sample\News\Model\Resource;
class Article extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    protected $_store  = null;
    protected $_date;
    protected $_storeManager;
    protected $dateTime;
    protected $_articleProductTable;
    protected $_productHelper;
    protected $_eventManager = null;


    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Sample\News\Helper\Product $productHelper
    ) {
        parent::__construct($resource);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->_articleProductTable = $this->getTable('sample_news_article_product');
        $this->_eventManager = $eventManager;
        $this->_productHelper = $productHelper;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sample_news_article', 'entity_id');
    }

    /**
     * Process page data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = array('article_id = ?' => (int) $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('sample_news_article_store'), $condition);
        return parent::_beforeDelete($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
//        /*
//         * For two attributes which represent timestamp data in DB
//         * we should make converting such as:
//         * If they are empty we need to convert them into DB
//         * type NULL so in DB they will be empty and not some default value
//         */
        //TODO: Transforma any date field
//        foreach (array('custom_theme_from', 'custom_theme_to') as $field) {
//            $value = !$object->getData($field) ? null : $object->getData($field);
//            $object->setData($field, $this->dateTime->formatDate($value));
//        }

        if (!$this->getIsUniqueArticleToStores($object)) {
            throw new \Magento\Framework\Exception(__('A page URL key for specified store already exists.'));
        }

        if (!$this->isValidArticleIdentifier($object)) {
            throw new \Magento\Framework\Exception(__('The page URL key contains capital letters or disallowed symbols.'));
        }

        if ($this->isNumericArticleIdentifier($object)) {
            throw new \Magento\Framework\Exception(__('The page URL key cannot be made of only numbers.'));
        }

        // modify create / update dates
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime($this->_date->gmtDate());
        }

        $object->setUpdateTime($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * Assign page to store views
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('sample_news_article_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                'article_id = ?'     => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'article_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }
        $this->_saveProductRelation($object);
        return parent::_afterSave($object);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
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
     * @param \Magento\Cms\Model\Page $object
     * @return \Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int)$object->getStoreId());
            $select->join(
                array('sample_news_article_store' => $this->getTable('sample_news_article_store')),
                $this->getMainTable() . '.entity_id = sample_news_article_store.article_id',
                array())
                ->where('status = ?', 1)
                ->where('sample_news_article_store.store_id IN (?)', $storeIds)
                ->order('sample_news_article_store.store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * @param $identifier
     * @param $store
     * @param null $isActive
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('main' => $this->getMainTable()))
            ->join(
                array('store' => $this->getTable('sample_news_article_store')),
                'main.entity_id = store.article_id',
                array())
            ->where('main.identifier = ?', $identifier)
            ->where('store.store_id IN (?)', $store);

        if (!is_null($isActive)) {
            $select->where('main.status = ?', $isActive);
        }

        return $select;
    }

    /**
     * Check for unique of identifier of page to selected store(s).
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    public function getIsUniqueArticleToStores(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($this->_storeManager->hasSingleStore() || !$object->hasStores()) {
            $stores = array(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->_getLoadByIdentifierSelect($object->getData('identifier'), $stores);

        if ($object->getId()) {
            $select->where('store.article_id <> ?', $object->getId());
        }

        if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return int
     */
    protected function isNumericArticleIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return int
     */
    protected function isValidArticleIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }



    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        $stores = array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId);
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(\Zend_Db_Select::COLUMNS)
            ->columns('main.entity_id')
            ->order('store.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves cms page title from DB by passed identifier.
     *
     * @param string $identifier
     * @return string|false
     */
    public function getArticleTitleByIdentifier($identifier)
    {
        $stores = array(\Magento\Core\Model\Store::DEFAULT_STORE_ID);
        if ($this->_store) {
            $stores[] = (int)$this->getStore()->getId();
        }

        $select = $this->_getLoadByIdentifierSelect($identifier, $stores);
        $select->reset(\Zend_Db_Select::COLUMNS)
            ->columns('main.title')
            ->order('store.store_id DESC')
            ->limit(1);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Retrieves cms page title from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getArticlePageTitleById($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getMainTable(), 'title')
            ->where('entity_id = :article_id');

        $binds = array(
            'entity_id' => (int) $id
        );

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Retrieves cms page identifier from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getArticleIdentifierById($id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getMainTable(), 'identifier')
            ->where('entity_id = :article_id');

        $binds = array(
            'article_id' => (int) $id
        );

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $articleId
     * @return array
     */
    public function lookupStoreIds($articleId)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('sample_news_article_store'), 'store_id')
            ->where('article_id = ?', (int)$articleId);

        return $adapter->fetchCol($select);
    }

    /**
     * Set store model
     *
     * @param \Magento\Core\Model\Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->_store);
    }

    protected function _saveProductRelation($article){
        $article->setIsChangedProductList(false);
        $id = $article->getId();
        $products = $article->getProductsData();

        if ($products === null) {
            return $this;
        }
        $oldProducts = $article->getProductsPosition();
        $insert = array_diff_key($products, $oldProducts);
        $delete = array_diff_key($oldProducts, $products);
        $update = array_intersect_key($products, $oldProducts);
        $update = array_diff_assoc($update, $oldProducts);

        $adapter = $this->_getWriteAdapter();
        if (!empty($delete)) {
            $cond = array('product_id IN(?)' => array_keys($delete), 'article_id=?' => $id);
            $adapter->delete($this->_articleProductTable, $cond);
        }
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $productId => $position) {
                $data[] = array(
                    'article_id' => (int)$id,
                    'product_id' => (int)$productId,
                    'position' => (int)$position
                );
            }
            $adapter->insertMultiple($this->_articleProductTable, $data);
        }

        if (!empty($update)) {
            foreach ($update as $productId => $position) {
                $where = array('article_id = ?' => (int)$id, 'product_id = ?' => (int)$productId);
                $bind = array('position' => (int)$position['position']);
                $adapter->update($this->_articleProductTable, $bind, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $productIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'sample_news_article_change_products',
                array('article' => $article, 'product_ids' => $productIds)
            );
        }

        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $article->setIsChangedProductList(true);
            $productIds = array_keys($insert + $delete + $update);
            $article->setAffectedProductIds($productIds);
        }
        return $this;
    }

    public function updateAttributes($articleIds, $attributes){

    }

    public function getProductsPosition($article)
    {
        $select = $this->_getWriteAdapter()->select()->from(
            $this->_articleProductTable,
            array('product_id', 'position')
        )->where(
                'article_id = :article_id'
            );
        $bind = array('article_id' => (int)$article->getId());

        return $this->_getWriteAdapter()->fetchPairs($select, $bind);
    }

    public function saveArticleProductRelation($product, $articles) {
        $product->setIsChangedArticleList(false);
        $id = $product->getId();
        if ($articles === null) {
            return $this;
        }
        $oldArticleObjects = $this->_productHelper->getSelectedArticles($product);
        if (!is_array($oldArticleObjects)) {
            $oldArticleObjects = array();
        }
        $oldArticles = array();
        foreach ($oldArticleObjects as $article) {
            $oldArticles[$article->getId()] = array('position' => $article->getPosition());
        }
//        echo "<pre>";
//        print_r($oldArticles);
//        print_r($articles);exit;
        $insert = array_diff_key($articles, $oldArticles);

        $delete = array_diff_key($oldArticles, $articles);
        $update = array_intersect_key($articles, $oldArticles);
        //TODO: check with this: https://bugs.php.net/bug.php?id=62115
        $update = array_diff_assoc($update, $oldArticles);


        $adapter = $this->_getWriteAdapter();
        if (!empty($delete)) {
            $condition = array('article_id IN(?)' => array_keys($delete), 'product_id=?' => $id);
            $adapter->delete($this->_articleProductTable, $condition);
        }
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $articleId => $position) {
                $data[] = array(
                    'product_id' => (int)$id,
                    'article_id' => (int)$articleId,
                    'position' => (int)$position
                );
            }
            $adapter->insertMultiple($this->_articleProductTable, $data);
        }

        if (!empty($update)) {
            foreach ($update as $articleId => $position) {
                $where = array('product_id = ?' => (int)$id, 'article_id = ?' => (int)$articleId);
                $bind = array('position' => (int)$position['position']);
                $adapter->update($this->_articleProductTable, $bind, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $articleIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'sample_news_product_change_articles',
                array('product' => $product, 'article_ids' => $articleIds)
            );
        }

        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $product->setIsChangedArticleList(true);
            $articleIds = array_keys($insert + $delete + $update);
            $product->setAffectedArticleIds($articleIds);
        }
        return $this;
    }
} 