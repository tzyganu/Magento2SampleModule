<?php
/**
 * Sample_News extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Sample
 * @package        Sample_News
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Sample\News\Model\Resource;
class Article
    extends \Magento\Framework\Model\Resource\Db\AbstractDb {
    /**
     * @var null
     */
    protected $_store  = null;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $_dateTime;
    /**
     * @var string
     */
    protected $_articleProductTable;
    /**
     * @var string
     */
    protected $_articleCategoryTable;
    /**
     * @var \Sample\News\Helper\Product
     */
    protected $_productHelper;
    /**
     * @var \Sample\News\Helper\Category
     */
    protected $_categoryHelper;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;


    /**
     * @access public
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Sample\News\Helper\Product $productHelper
     * @param \Sample\News\Helper\Category $categoryHelper
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Sample\News\Helper\Product $productHelper,
        \Sample\News\Helper\Category $categoryHelper
    ) {
        parent::__construct($resource);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->_dateTime = $dateTime;
        $this->_eventManager = $eventManager;
        $this->_productHelper = $productHelper;
        $this->_categoryHelper = $categoryHelper;
        $this->_articleProductTable = $this->getTable('sample_news_article_product');
        $this->_articleCategoryTable = $this->getTable('sample_news_article_category');

    }

    /**
     * Initialize resource model
     * @access protected
     * @return void
     */
    protected function _construct() {
        $this->_init('sample_news_article', 'entity_id');
    }

    /**
     * Process page data before deleting
     * @access protected
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object) {
        $condition = ['article_id = ?' => (int) $object->getId()];
        $this->_getWriteAdapter()->delete($this->getTable('sample_news_article_store'), $condition);
        return parent::_beforeDelete($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object){
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime($this->_date->gmtDate());
        }
        $urlKey = $object->getData('identifier');
        if ($urlKey == '') {
            $urlKey = $object->getTitle();
        }
        $urlKey = $object->formatUrlKey($urlKey);
        $object->setIdentifier($urlKey);
        $validKey = false;
        while (!$validKey) {
            if ($this->getIsUniqueArticleToStores($object)) {
                $validKey = true;
            }
            else {
                $parts = explode('-', $urlKey);
                $last = $parts[count($parts) - 1];
                if (!is_numeric($last)){
                    $urlKey = $urlKey.'-1';
                }
                else {
                    $suffix = '-'.($last + 1);
                    unset($parts[count($parts) - 1]);
                    $urlKey = implode('-', $parts).$suffix;
                }
                $object->setData('identifier', $urlKey);
            }
        }
        $object->setUpdateTime($this->_date->gmtDate());
        return parent::_beforeSave($object);
    }

    /**
     * Assign page to store views
     * @access public
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object) {
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
        $this->_saveCategoryRelation($object);
        return parent::_afterSave($object);
    }

    /**
     * Perform operations after object load
     * @access protected
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object) {
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
     * @return \Magento\Framework\Db\Select
     */
    protected function _getLoadSelect($field, $value, $object) {
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
     * @access protected
     * @param $identifier
     * @param $store
     * @param null $isActive
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null) {
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
     * @access public
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    public function getIsUniqueArticleToStores(\Magento\Framework\Model\AbstractModel $object) {
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
    public function getArticleTitleByIdentifier($identifier) {
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
    public function getArticlePageTitleById($id) {
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
    public function getArticleIdentifierById($id) {
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
    public function lookupStoreIds($articleId) {
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
    public function setStore($store) {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore() {
        return $this->_storeManager->getStore($this->_store);
    }

    protected function _saveProductRelation($article) {
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
            $condition = array('product_id IN(?)' => array_keys($delete), 'article_id=?' => $id);
            $adapter->delete($this->_articleProductTable, $condition);
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

    protected function _saveCategoryRelation($article){
        $article->setIsChangedCategoryList(false);
        $id = $article->getId();
        $categories = $article->getCategoriesIds();

        if ($categories === null) {
            return $this;
        }
        $oldCategoryIds = $article->getCategoryIds();
        $insert = array_diff_key($categories, $oldCategoryIds);
        $delete = array_diff_key($oldCategoryIds, $categories);

        $adapter = $this->_getWriteAdapter();
        if (!empty($delete)) {
            $condition = array('category_id IN(?)' => $delete, 'article_id=?' => $id);
            $adapter->delete($this->_articleCategoryTable, $condition);
        }
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $categoryId) {
                $data[] = array(
                    'article_id' => (int)$id,
                    'category_id' => (int)$categoryId,
                    'position' => 1
                );
            }
            $adapter->insertMultiple($this->_articleCategoryTable, $data);
        }

        if (!empty($insert) || !empty($delete)) {
            $categoryIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'sample_news_article_change_categories',
                array('article' => $article, 'category_ids' => $categoryIds)
            );
        }

        if (!empty($insert) /*|| !empty($update)*/ || !empty($delete)) {
            $article->setIsChangedCategoryList(true);
            $categoryIds = array_keys($insert + $delete /* + $update*/);
            $article->setAffectedCategoryIds($categoryIds);
        }
        return $this;
    }

    public function getProductsPosition($article) {
        $select = $this->_getWriteAdapter()->select()->from(
            $this->_articleProductTable,
            array('product_id', 'position')
        )->where(
            'article_id = :article_id'
        );
        $bind = array('article_id' => (int)$article->getId());

        return $this->_getWriteAdapter()->fetchPairs($select, $bind);
    }

    /**
     * @param $product
     * @param $articles
     * @return $this
     */
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
        $insert = array_diff_key($articles, $oldArticles);

        $delete = array_diff_key($oldArticles, $articles);
        $update = array_intersect_key($articles, $oldArticles);
        $toUpdate = array();
        foreach ($update as $productId => $values) {
            if (isset($oldArticles[$productId]) && $oldArticles[$productId]['position'] != $values['position']) {
                $toUpdate[$productId] = array();
                $toUpdate[$productId]['position'] = $values['position'];
            }
        }

        $update = $toUpdate;
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

    /**
     * @param $category
     * @param $articles
     * @return $this
     */
    public function saveArticleCategoryRelation($category, $articles) {
        $category->setIsChangedArticleList(false);
        $id = $category->getId();
        if ($articles === null) {
            return $this;
        }
        $oldArticleObjects = $this->_categoryHelper->getSelectedArticles($category);
        if (!is_array($oldArticleObjects)) {
            $oldArticleObjects = array();
        }
        $oldArticles = array();
        foreach ($oldArticleObjects as $article) {
            $oldArticles[$article->getId()] = $article->getPosition();
        }
        $insert = array_diff_key($articles, $oldArticles);
        $delete = array_diff_key($oldArticles, $articles);
        $update = array_intersect_key($articles, $oldArticles);
        $update = array_diff_assoc($update, $oldArticles);


        $adapter = $this->_getWriteAdapter();
        if (!empty($delete)) {
            $condition = array('article_id IN(?)' => array_keys($delete), 'category_id=?' => $id);
            $adapter->delete($this->_articleCategoryTable, $condition);
        }
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $articleId => $position) {
                $data[] = array(
                    'category_id' => (int)$id,
                    'article_id' => (int)$articleId,
                    'position' => (int)$position
                );
            }
            $adapter->insertMultiple($this->_articleCategoryTable, $data);
        }

        if (!empty($update)) {
            foreach ($update as $articleId => $position) {
                $where = array('category_id = ?' => (int)$id, 'article_id = ?' => (int)$articleId);
                $bind = array('position' => (int)$position);
                $adapter->update($this->_articleCategoryTable, $bind, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $articleIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'sample_news_category_change_articles',
                array('category' => $category, 'article_ids' => $articleIds)
            );
        }

        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $category->setIsChangedArticleList(true);
            $articleIds = array_keys($insert + $delete + $update);
            $category->setAffectedArticleIds($articleIds);
        }
        return $this;
    }

    /**
     * @access public
     * @param $article
     * @return array
     */
    public function getCategoryIds($article) {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->_articleCategoryTable,
            'category_id'
        )->where(
            'article_id = ?',
            (int)$article->getId()
        );
        return $adapter->fetchCol($select);
    }
}
