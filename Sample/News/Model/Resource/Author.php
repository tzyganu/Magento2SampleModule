<?php
namespace Sample\News\Model\Resource;

use Magento\Framework\Model\Resource\Db\AbstractDb;
use Magento\Framework\Model\Resource\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime as LibDateTime;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;
use Sample\News\Model\Author as AuthorModel;
use Magento\Framework\Event\ManagerInterface;
use Magento\Catalog\Model\Product;
use Sample\News\Model\Author\Product as AuthorProduct;
use Sample\News\Model\Author\Category as AuthorCategory;

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
     * @var string
     */
    protected $authorProductTable;

    /**
     * @var string
     */
    protected $authorCategoryTable;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Sample\News\Model\Author\Product
     */
    protected $authorProduct;

    /**
     * @param Context $context
     * @param DateTime $date
     * @param StoreManagerInterface $storeManager
     * @param LibDateTime $dateTime
     * @param ManagerInterface $eventManager
     * @param AuthorProduct $authorProduct
     * @param AuthorCategory $authorCategory
     */
    public function __construct(
        Context $context,
        DateTime $date,
        StoreManagerInterface $storeManager,
        LibDateTime $dateTime,
        ManagerInterface $eventManager,
        AuthorProduct $authorProduct,
        AuthorCategory $authorCategory
    )
    {
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->eventManager = $eventManager;
        $this->authorProduct = $authorProduct;
        $this->authorCategory = $authorCategory;
        parent::__construct($context);
        $this->authorProductTable = $this->getTable('sample_news_author_product');
        $this->authorCategoryTable = $this->getTable('sample_news_author_category');

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
                if (!is_numeric($last)) {
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
        $this->saveStoreRelation($object);
        $this->saveProductRelation($object);
        $this->saveCategoryRelation($object);
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

    /**
     * @param AuthorModel $author
     * @return array
     */
    public function getProductsPosition(AuthorModel $author)
    {
        $select = $this->_getWriteAdapter()->select()->from(
            $this->authorProductTable,
            ['product_id', 'position']
        )
        ->where(
            'author_id = :author_id'
        );
        $bind = ['author_id' => (int)$author->getId()];
        return $this->_getWriteAdapter()->fetchPairs($select, $bind);
    }

    /**
     * @param AuthorModel $author
     * @return $this
     */
    protected function saveStoreRelation(AuthorModel $author)
    {
        $oldStores = $this->lookupStoreIds($author->getId());
        $newStores = (array)$author->getStores();
        if (empty($newStores)) {
            $newStores = (array)$author->getStoreId();
        }
        $table = $this->getTable('sample_news_author_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = [
                'author_id = ?' => (int)$author->getId(),
                'store_id IN (?)' => $delete
            ];
            $this->_getWriteAdapter()->delete($table, $where);
        }
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'author_id' => (int)$author->getId(),
                    'store_id' => (int)$storeId
                ];
            }
            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }
        return $this;
    }

    /**
     * @param AuthorModel $author
     * @return $this
     */
    protected function saveProductRelation(AuthorModel $author)
    {
        $author->setIsChangedProductList(false);
        $id = $author->getId();
        $products = $author->getProductsData();

        if ($products === null) {
            return $this;
        }
        $oldProducts = $author->getProductsPosition();
        $insert = array_diff_key($products, $oldProducts);
        $delete = array_diff_key($oldProducts, $products);
        //TODO: make update code prettier.
        $update = array_intersect_key($products, $oldProducts);
        $_update = array();
        foreach ($update as $key=>$settings) {
            if (isset($oldProducts[$key]) && $oldProducts[$key] != $settings['position']) {
                $_update[$key] = $settings;
            }
        }
        $update = $_update;
        $adapter = $this->_getWriteAdapter();
        if (!empty($delete)) {
            $condition = ['product_id IN(?)' => array_keys($delete), 'author_id=?' => $id];
            $adapter->delete($this->authorProductTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $productId => $position) {
                $data[] = [
                    'author_id' => (int)$id,
                    'product_id' => (int)$productId,
                    'position' => (int)$position
                ];
            }
            $adapter->insertMultiple($this->authorProductTable, $data);
        }

        if (!empty($update)) {
            foreach ($update as $productId => $position) {
                $where = ['author_id = ?' => (int)$id, 'product_id = ?' => (int)$productId];
                $bind = ['position' => (int)$position['position']];
                $adapter->update($this->authorProductTable, $bind, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $productIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'sample_news_author_change_products',
                ['author' => $author, 'product_ids' => $productIds]
            );
        }

        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $author->setIsChangedProductList(true);
            $productIds = array_keys($insert + $delete + $update);
            $author->setAffectedProductIds($productIds);
        }
        return $this;
    }

    /**
     * @param Product $product
     * @param $authors
     * @return $this
     */
    public function saveAuthorProductRelation(Product $product, $authors)
    {
        $product->setIsChangedAuthorList(false);
        $id = $product->getId();
        if ($authors === null) {
            return $this;
        }
        $oldAuthorObjects = $this->authorProduct->getSelectedAuthors($product);
        if (!is_array($oldAuthorObjects)) {
            $oldAuthorObjects = [];
        }
        $oldAuthors = [];
        foreach ($oldAuthorObjects as $author) {
            /** @var \Sample\News\Model\Author $author */
            $oldAuthors[$author->getId()] = ['position' => $author->getPosition()];
        }
        $insert = array_diff_key($authors, $oldAuthors);

        $delete = array_diff_key($oldAuthors, $authors);

        $update = array_intersect_key($authors, $oldAuthors);
        $toUpdate = [];
        foreach ($update as $productId => $values) {
            if (isset($oldAuthors[$productId]) && $oldAuthors[$productId]['position'] != $values['position']) {
                $toUpdate[$productId] = [];
                $toUpdate[$productId]['position'] = $values['position'];
            }
        }

        $update = $toUpdate;
        $adapter = $this->_getWriteAdapter();
        if (!empty($delete)) {
            $condition = ['author_id IN(?)' => array_keys($delete), 'product_id=?' => $id];
            $adapter->delete($this->authorProductTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $authorId => $position) {
                $data[] = [
                    'product_id' => (int)$id,
                    'author_id' => (int)$authorId,
                    'position' => (int)$position['position']
                ];
            }
            $adapter->insertMultiple($this->authorProductTable, $data);
        }

        if (!empty($update)) {
            foreach ($update as $authorId => $position) {
                $where = ['product_id = ?' => (int)$id, 'author_id = ?' => (int)$authorId];
                $bind = ['position' => (int)$position['position']];
                $adapter->update($this->authorProductTable, $bind, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $authorIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'sample_news_product_change_authors',
                ['product' => $product, 'author_ids' => $authorIds]
            );
        }

        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $product->setIsChangedAuthorList(true);
            $authorIds = array_keys($insert + $delete + $update);
            $product->setAffectedAuthorIds($authorIds);
        }
        return $this;
    }

    protected function saveCategoryRelation(AuthorModel $author)
    {
        $author->setIsChangedCategoryList(false);
        $id = $author->getId();
        $categories = $author->getCategoriesIds();

        if ($categories === null) {
            return $this;
        }
        $oldCategoryIds = $author->getCategoryIds();
        $insert = array_diff_key($categories, $oldCategoryIds);
        $delete = array_diff_key($oldCategoryIds, $categories);

        $adapter = $this->_getWriteAdapter();
        if (!empty($delete)) {
            $condition = array('category_id IN(?)' => $delete, 'author_id=?' => $id);
            $adapter->delete($this->authorCategoryTable, $condition);
        }
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $categoryId) {
                $data[] = array(
                    'author_id' => (int)$id,
                    'category_id' => (int)$categoryId,
                    'position' => 1
                );
            }
            $adapter->insertMultiple($this->authorCategoryTable, $data);
        }

        if (!empty($insert) || !empty($delete)) {
            $categoryIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'sample_news_author_change_categories',
                array('author' => $author, 'category_ids' => $categoryIds)
            );
        }

        if (!empty($insert) /*|| !empty($update)*/ || !empty($delete)) {
            $author->setIsChangedCategoryList(true);
            $categoryIds = array_keys($insert + $delete /* + $update*/);
            $author->setAffectedCategoryIds($categoryIds);
        }
        return $this;
    }

    /**
     * @param AuthorModel $author
     *
     * @return array
     */
    public function getCategoryIds(AuthorModel $author)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->authorCategoryTable,
            'category_id'
        )
        ->where(
            'author_id = ?',
            (int)$author->getId()
        );
        return $adapter->fetchCol($select);
    }

    /**
     * @param $category
     * @param $authors
     * @return $this
     */
    public function saveAuthorCategoryRelation($category, $authors)
    {
        $category->setIsChangedAuthorList(false);
        $id = $category->getId();
        if ($authors === null) {
            return $this;
        }
        $oldAuthorObjects = $this->authorCategory->getSelectedAuthors($category);
        if (!is_array($oldAuthorObjects)) {
            $oldAuthorObjects = array();
        }
        $oldAuthors = [];
        foreach ($oldAuthorObjects as $author) {
            /** @var \Sample\News\Model\Author $author */
            $oldAuthors[$author->getId()] = $author->getPosition();
        }
        $insert = array_diff_key($authors, $oldAuthors);
        $delete = array_diff_key($oldAuthors, $authors);
        $update = array_intersect_key($authors, $oldAuthors);
        $update = array_diff_assoc($update, $oldAuthors);


        $adapter = $this->_getWriteAdapter();
        if (!empty($delete)) {
            $condition = array('author_id IN(?)' => array_keys($delete), 'author_id=?' => $id);
            $adapter->delete($this->authorCategoryTable, $condition);
        }
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $authorId => $position) {
                $data[] = [
                    'category_id' => (int)$id,
                    'author_id' => (int)$authorId,
                    'position' => (int)$position
                ];
            }
            $adapter->insertMultiple($this->authorCategoryTable, $data);
        }

        if (!empty($update)) {
            foreach ($update as $authorId => $position) {
                $where = ['category_id = ?' => (int)$id, 'author_id = ?' => (int)$authorId];
                $bind = ['position' => (int)$position];
                $adapter->update($this->authorCategoryTable, $bind, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $authorIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'sample_news_category_change_authors',
                array('category' => $category, 'author_ids' => $authorIds)
            );
        }

        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $category->setIsChangedAuthorList(true);
            $authorIds = array_keys($insert + $delete + $update);
            $category->setAffectedAuthorIds($authorIds);
        }
        return $this;
    }

}
