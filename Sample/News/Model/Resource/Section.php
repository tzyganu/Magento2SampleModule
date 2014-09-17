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
class Section
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
    protected $_sectionProductTable;
    /**
     * @var string
     */
    protected $_sectionCategoryTable;
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

    protected $_sectionCollectionFactory;


    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param Section\CollectionFactory $sectionCollectionFactory
     * @param \Sample\News\Helper\Product $productHelper
     * @param \Sample\News\Helper\Category $categoryHelper
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Sample\News\Model\Resource\Section\CollectionFactory $sectionCollectionFactory,
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
        $this->_sectionCollectionFactory = $sectionCollectionFactory;
        $this->_sectionProductTable = $this->getTable('sample_news_section_product');
        $this->_sectionCategoryTable = $this->getTable('sample_news_section_category');

    }

    /**
     * Initialize resource model
     * @access protected
     * @return void
     */
    protected function _construct() {
        $this->_init('sample_news_section', 'entity_id');
    }

    /**
     * Process page data before deleting
     * @access protected
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object) {
        $condition = ['section_id = ?' => (int) $object->getId()];
        $this->_getWriteAdapter()->delete($this->getTable('sample_news_section_store'), $condition);
        parent::_beforeDelete($object);

        /**
         * Update children count for all parent sections
         */
        $parentIds = $object->getParentIds();
        if ($parentIds) {
            $childDecrease = $object->getChildrenCount() + 1;
            // +1 is itself
            $data = array('children_count' => new \Zend_Db_Expr('children_count - ' . $childDecrease));
            $where = array('entity_id IN(?)' => $parentIds);
            $this->_getWriteAdapter()->update($this->getMainTable(), $data, $where);
        }
        $this->deleteChildren($object);
        return $this;
    }

    public function deleteChildren(\Magento\Framework\Object $object)
    {
        $adapter = $this->_getWriteAdapter();
        $pathField = $adapter->quoteIdentifier('path');

        $select = $adapter->select()->from(
            $this->getMainTable(),
            array('entity_id')
        )->where(
                $pathField . ' LIKE :c_path'
            );

        $childrenIds = $adapter->fetchCol($select, array('c_path' => $object->getPath() . '/%'));

        if (!empty($childrenIds)) {
            $adapter->delete($this->getMainTable(), array('entity_id IN (?)' => $childrenIds));
        }

        /**
         * Add deleted children ids to object
         * This data can be used in after delete event
         */
        $object->setDeletedChildrenIds($childrenIds);
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object){
        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime($this->_date->gmtDate());
        }
        $object->setUpdateTime($this->_date->gmtDate());
        $urlKey = $object->getData('identifier');
        if ($urlKey == '') {
            $urlKey = $object->getName();
        }
        $urlKey = $object->formatUrlKey($urlKey);
        $object->setIdentifier($urlKey);
        $validKey = false;
        while (!$validKey) {
            if ($this->getIsUniqueSectionToStores($object)) {
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
        parent::_beforeSave($object);

        if (!$object->getChildrenCount()) {
            $object->setChildrenCount(0);
        }
        if ($object->getLevel() === null) {
            $object->setLevel(1);
        }

        if (!$object->getId()) {
            $object->setPosition($this->_getMaxPosition($object->getPath()) + 1);
            $path = explode('/', $object->getPath());
            $level = count($path);
            $object->setLevel($level);
            if ($level) {
                $object->setParentId($path[$level - 1]);
            }
            $object->setPath($object->getPath() . '/');

            $toUpdateChild = explode('/', $object->getPath());

            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('children_count' => new \Zend_Db_Expr('children_count+1')),
                array('entity_id IN(?)' => $toUpdateChild)
            );
        }
        return $this;
    }

    /**
     * Assign page to store views
     * @access public
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object) {
        if (substr($object->getPath(), -1) == '/') {
            $object->setPath($object->getPath() . $object->getId());
            $this->_savePath($object);
        }
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('sample_news_section_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = array(
                'section_id = ?'     => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $storeId) {
                $data[] = array(
                    'section_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }
        $this->_saveProductRelation($object);
        $this->_saveCategoryRelation($object);
        return parent::_afterSave($object);
    }
    protected function _savePath($object) {
        if ($object->getId()) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array('path' => $object->getPath()),
                array('entity_id = ?' => $object->getId())
            );
            $object->unsetData('path_ids');
        }
        return $this;
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
                array('sample_news_section_store' => $this->getTable('sample_news_section_store')),
                $this->getMainTable() . '.entity_id = sample_news_section_store.section_id',
                array())
                ->where('status = ?', 1)
                ->where('sample_news_section_store.store_id IN (?)', $storeIds)
                ->order('sample_news_section_store.store_id DESC')
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
                array('store' => $this->getTable('sample_news_section_store')),
                'main.entity_id = store.section_id',
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
    public function getIsUniqueSectionToStores(\Magento\Framework\Model\AbstractModel $object) {
        if ($this->_storeManager->hasSingleStore() || !$object->hasStores()) {
            $stores = array(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->_getLoadByIdentifierSelect($object->getData('identifier'), $stores);

        if ($object->getId()) {
            $select->where('store.section_id <> ?', $object->getId());
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
     * Retrieves section name from DB by passed identifier.
     *
     * @param string $identifier
     * @return string|false
     */
    public function getSectionNameByIdentifier($identifier) {
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
     * Retrieves section name from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getSectionPageNameById($id) {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('entity_id = :section_id');

        $binds = array(
            'entity_id' => (int) $id
        );

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Retrieves section identifier from DB by passed id.
     *
     * @param string $id
     * @return string|false
     */
    public function getSectionIdentifierById($id) {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getMainTable(), 'identifier')
            ->where('entity_id = :section_id');

        $binds = array(
            'section_id' => (int) $id
        );

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $sectionId
     * @return array
     */
    public function lookupStoreIds($sectionId) {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('sample_news_section_store'), 'store_id')
            ->where('section_id = ?', (int)$sectionId);

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

    protected function _saveProductRelation($section) {
        $section->setIsChangedProductList(false);
        $id = $section->getId();
        $products = $section->getProductsData();

        if ($products === null) {
            return $this;
        }
        $oldProducts = $section->getProductsPosition();
        $insert = array_diff_key($products, $oldProducts);
        $delete = array_diff_key($oldProducts, $products);
        $update = array_intersect_key($products, $oldProducts);
        $update = array_diff_assoc($update, $oldProducts);

        $adapter = $this->_getWriteAdapter();
        if (!empty($delete)) {
            $condition = array('product_id IN(?)' => array_keys($delete), 'section_id=?' => $id);
            $adapter->delete($this->_sectionProductTable, $condition);
        }
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $productId => $position) {
                $data[] = array(
                    'section_id' => (int)$id,
                    'product_id' => (int)$productId,
                    'position' => (int)$position
                );
            }
            $adapter->insertMultiple($this->_sectionProductTable, $data);
        }

        if (!empty($update)) {
            foreach ($update as $productId => $position) {
                $where = array('section_id = ?' => (int)$id, 'product_id = ?' => (int)$productId);
                $bind = array('position' => (int)$position['position']);
                $adapter->update($this->_sectionProductTable, $bind, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $productIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'sample_news_section_change_products',
                array('section' => $section, 'product_ids' => $productIds)
            );
        }

        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $section->setIsChangedProductList(true);
            $productIds = array_keys($insert + $delete + $update);
            $section->setAffectedProductIds($productIds);
        }
        return $this;
    }

    protected function _saveCategoryRelation($section){
        $section->setIsChangedCategoryList(false);
        $id = $section->getId();
        $categories = $section->getCategoriesIds();

        if ($categories === null) {
            return $this;
        }
        $oldCategoryIds = $section->getCategoryIds();
        $insert = array_diff_key($categories, $oldCategoryIds);
        $delete = array_diff_key($oldCategoryIds, $categories);

        $adapter = $this->_getWriteAdapter();
        if (!empty($delete)) {
            $condition = array('category_id IN(?)' => $delete, 'section_id=?' => $id);
            $adapter->delete($this->_sectionCategoryTable, $condition);
        }
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $categoryId) {
                $data[] = array(
                    'section_id' => (int)$id,
                    'category_id' => (int)$categoryId,
                    'position' => 1
                );
            }
            $adapter->insertMultiple($this->_sectionCategoryTable, $data);
        }

        if (!empty($insert) || !empty($delete)) {
            $categoryIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'sample_news_section_change_categories',
                array('section' => $section, 'section_ids' => $categoryIds)
            );
        }

        if (!empty($insert) /*|| !empty($update)*/ || !empty($delete)) {
            $section->setIsChangedCategoryList(true);
            $categoryIds = array_keys($insert + $delete /* + $update*/);
            $section->setAffectedCategoryIds($categoryIds);
        }
        return $this;
    }

    public function getProductsPosition($section) {
        $select = $this->_getWriteAdapter()->select()->from(
            $this->_sectionProductTable,
            array('product_id', 'position')
        )->where(
                'section_id = :section_id'
            );
        $bind = array('section_id' => (int)$section->getId());

        return $this->_getWriteAdapter()->fetchPairs($select, $bind);
    }

    /**
     * @param $product
     * @param $sections
     * @return $this
     */
    public function saveSectionProductRelation($product, $sections) {
        $product->setIsChangedSectionList(false);
        $id = $product->getId();
        if ($sections === null) {
            return $this;
        }
        $oldSectionObjects = $this->_productHelper->getSelectedSections($product);
        if (!is_array($oldSectionObjects)) {
            $oldSectionObjects = array();
        }
        $oldSections = array();
        foreach ($oldSectionObjects as $section) {
            $oldSections[$section->getId()] = array('position' => $section->getPosition());
        }
        $insert = array_diff_key($sections, $oldSections);

        $delete = array_diff_key($oldSections, $sections);
        $update = array_intersect_key($sections, $oldSections);
        $toUpdate = array();
        foreach ($update as $productId => $values) {
            if (isset($oldSections[$productId]) && $oldSections[$productId]['position'] != $values['position']) {
                $toUpdate[$productId] = array();
                $toUpdate[$productId]['position'] = $values['position'];
            }
        }

        $update = $toUpdate;
        $adapter = $this->_getWriteAdapter();
        if (!empty($delete)) {
            $condition = array('section_id IN(?)' => array_keys($delete), 'product_id=?' => $id);
            $adapter->delete($this->_sectionProductTable, $condition);
        }
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $sectionId => $position) {
                $data[] = array(
                    'product_id' => (int)$id,
                    'section_id' => (int)$sectionId,
                    'position' => (int)$position
                );
            }
            $adapter->insertMultiple($this->_sectionProductTable, $data);
        }

        if (!empty($update)) {
            foreach ($update as $sectionId => $position) {
                $where = array('product_id = ?' => (int)$id, 'section_id = ?' => (int)$sectionId);
                $bind = array('position' => (int)$position['position']);
                $adapter->update($this->_sectionProductTable, $bind, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $sectionIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'sample_news_product_change_sections',
                array('product' => $product, 'section_ids' => $sectionIds)
            );
        }

        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $product->setIsChangedSectionList(true);
            $sectionIds = array_keys($insert + $delete + $update);
            $product->setAffectedSectionIds($sectionIds);
        }
        return $this;
    }

    /**
     * @param $category
     * @param $sections
     * @return $this
     */
    public function saveSectionCategoryRelation($category, $sections) {
        $category->setIsChangedSectionList(false);
        $id = $category->getId();
        if ($sections === null) {
            return $this;
        }
        $oldSectionObjects = $this->_categoryHelper->getSelectedSections($category);
        if (!is_array($oldSectionObjects)) {
            $oldSectionObjects = array();
        }
        $oldSections = array();
        foreach ($oldSectionObjects as $section) {
            $oldSections[$section->getId()] = $section->getPosition();
        }
        $insert = array_diff_key($sections, $oldSections);
        $delete = array_diff_key($oldSections, $sections);
        $update = array_intersect_key($sections, $oldSections);
        $update = array_diff_assoc($update, $oldSections);


        $adapter = $this->_getWriteAdapter();
        if (!empty($delete)) {
            $condition = array('section_id IN(?)' => array_keys($delete), 'category_id=?' => $id);
            $adapter->delete($this->_sectionCategoryTable, $condition);
        }
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $sectionId => $position) {
                $data[] = array(
                    'category_id' => (int)$id,
                    'section_id' => (int)$sectionId,
                    'position' => (int)$position
                );
            }
            $adapter->insertMultiple($this->_sectionCategoryTable, $data);
        }

        if (!empty($update)) {
            foreach ($update as $sectionId => $position) {
                $where = array('category_id = ?' => (int)$id, 'section_id = ?' => (int)$sectionId);
                $bind = array('position' => (int)$position);
                $adapter->update($this->_sectionCategoryTable, $bind, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $sectionIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->_eventManager->dispatch(
                'sample_news_category_change_sections',
                array('category' => $category, 'section_ids' => $sectionIds)
            );
        }

        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $category->setIsChangedSectionList(true);
            $sectionIds = array_keys($insert + $delete + $update);
            $category->setAffectedSectionIds($sectionIds);
        }
        return $this;
    }

    /**
     * @access public
     * @param $section
     * @return array
     */
    public function getCategoryIds($section) {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from(
            $this->_sectionCategoryTable,
            'category_id'
        )->where(
            'section_id = ?',
            (int)$section->getId()
        );
        return $adapter->fetchCol($select);
    }
    protected function _getMaxPosition($path)
    {
        $adapter = $this->getReadConnection();
        $positionField = $adapter->quoteIdentifier('position');
        $level = count(explode('/', $path));
        $bind = array('c_level' => $level, 'c_path' => $path . '/%');
        $select = $adapter->select()->from(
            $this->getTable('sample_news_section'),
            'MAX(' . $positionField . ')'
        )->where(
                $adapter->quoteIdentifier('path') . ' LIKE :c_path'
            )->where(
                $adapter->quoteIdentifier('level') . ' = :c_level'
            );

        $position = $adapter->fetchOne($select, $bind);
        if (!$position) {
            $position = 0;
        }
        return $position;
    }
    public function isForbiddenToDelete($categoryId)
    {
        return $categoryId == \Sample\News\Helper\Section::ROOT_SECTION_ID;
    }

    /**
     * @param \Sample\News\Model\Section $section
     * @param \Sample\News\Model\Section $newParent
     * @param null $afterSectionId
     * @return $this
     */
    public function changeParent(
        \Sample\News\Model\Section $section,
        \Sample\News\Model\Section $newParent,
        $afterSectionId = null
    ) {
        $childrenCount = $this->getChildrenCount($section->getId()) + 1;
        $table = $this->getMainTable();
        $adapter = $this->_getWriteAdapter();
        $levelFiled = $adapter->quoteIdentifier('level');
        $pathField = $adapter->quoteIdentifier('path');

        /**
         * Decrease children count for all old section parent sections
         */
        $adapter->update(
            $table,
            array('children_count' => new \Zend_Db_Expr('children_count - ' . $childrenCount)),
            array('entity_id IN(?)' => $section->getParentIds())
        );

        /**
         * Increase children count for new category parents
         */
        $adapter->update(
            $table,
            array('children_count' => new \Zend_Db_Expr('children_count + ' . $childrenCount)),
            array('entity_id IN(?)' => $newParent->getPathIds())
        );

        $position = $this->_processPositions($section, $newParent, $afterSectionId);

        $newPath = sprintf('%s/%s', $newParent->getPath(), $section->getId());
        $newLevel = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $section->getLevel();

        /**
         * Update children nodes path
         */
        $adapter->update(
            $table,
            array(
                'path' => new \Zend_Db_Expr(
                    'REPLACE(' . $pathField . ',' . $adapter->quote(
                        $section->getPath() . '/'
                    ) . ', ' . $adapter->quote(
                        $newPath . '/'
                    ) . ')'
                ),
                'level' => new \Zend_Db_Expr($levelFiled . ' + ' . $levelDisposition)
            ),
            array($pathField . ' LIKE ?' => $section->getPath() . '/%')
        );
        /**
         * Update moved category data
         */
        $data = array(
            'path' => $newPath,
            'level' => $newLevel,
            'position' => $position,
            'parent_id' => $newParent->getId()
        );
        $adapter->update($table, $data, array('entity_id = ?' => $section->getId()));

        // Update category object to new data
        $section->addData($data);
        $section->unsetData('path_ids');

        return $this;
    }


    protected function _processPositions($section, $newParent, $afterSectionId)
    {
        $table = $this->getMainTable();
        $adapter = $this->_getWriteAdapter();
        $positionField = $adapter->quoteIdentifier('position');

        $bind = array('position' => new \Zend_Db_Expr($positionField . ' - 1'));
        $where = array(
            'parent_id = ?' => $section->getParentId(),
            $positionField . ' > ?' => $section->getPosition()
        );
        $adapter->update($table, $bind, $where);

        /**
         * Prepare position value
         */
        if ($afterSectionId) {
            $select = $adapter->select()->from($table, 'position')->where('entity_id = :entity_id');
            $position = $adapter->fetchOne($select, array('entity_id' => $afterSectionId));
            $position += 1;
        } else {
            $position = 1;
        }

        $bind = array('position' => new \Zend_Db_Expr($positionField . ' + 1'));
        $where = array('parent_id = ?' => $newParent->getId(), $positionField . ' >= ?' => $position);
        $adapter->update($table, $bind, $where);

        return $position;
    }
    public function getChildrenCount($sectionId)
    {
        $select = $this->_getReadAdapter()->select()->from(
            $this->getMainTable(),
            'children_count'
        )->where(
                'entity_id = :entity_id'
            );
        $bind = array('entity_id' => $sectionId);

        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    public function getChildrenSections($section)
    {
        $collection = $section->getCollection();
        $collection->addFieldToFilter('status', 1)
            ->addIdFilter($section->getChildren())->setOrder('position', \Magento\Framework\DB\Select::SQL_ASC)
            ->load();

        return $collection;
    }

    public function getChildren($section, $recursive = true)
    {
        $adapter = $this->_getReadAdapter();
        $bind = array(
            'c_path' => $section->getPath() . '/%'
        );
        $select = $this->_getReadAdapter()->select()->from(
            array('m' => $this->getMainTable()),
            'entity_id'
            )->where(
                $adapter->quoteIdentifier('path') . ' LIKE :c_path'
            );
        if (!$recursive) {
            $select->where($adapter->quoteIdentifier('level') . ' <= :c_level');
            $bind['c_level'] = $section->getLevel() + 1;
        }
        return $adapter->fetchCol($select, $bind);
    }
    public function getParentSections($section){
        $pathIds = array_reverse(explode('/', $section->getPath()));
        $sections = $this->_sectionCollectionFactory->create()
            ->addFieldToFilter('entity_id', array('in' => $pathIds))
            ->load()
            ->getItems();
        return $sections;
    }
}
