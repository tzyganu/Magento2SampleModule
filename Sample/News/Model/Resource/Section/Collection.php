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
namespace Sample\News\Model\Resource\Section;
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection {
    protected $_joinedFields = array();
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sample_news_section_collection';

    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject = 'section_collection';


    /**
     * Init collection and determine table names
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('\Sample\News\Model\Section', '\Sample\News\Model\Resource\Section');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Add Id filter
     *
     * @param array $sectionIds
     * @return $this
     */
    public function addIdFilter($sectionIds)
    {
        if (is_array($sectionIds)) {
            if (empty($sectionIds)) {
                $condition = '';
            } else {
                $condition = array('in' => $sectionIds);
            }
        } elseif (is_numeric($sectionIds)) {
            $condition = $sectionIds;
        } elseif (is_string($sectionIds)) {
            $ids = explode(',', $sectionIds);
            if (empty($ids)) {
                $condition = $sectionIds;
            } else {
                $condition = array('in' => $ids);
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }

    /**
     * Before collection load
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        $this->_eventManager->dispatch($this->_eventPrefix . '_load_before', array($this->_eventObject => $this));
        return parent::_beforeLoad();
    }

    /**
     * After collection load
     *
     * @return $this
     */
    protected function _afterLoad() {
        $this->_eventManager->dispatch($this->_eventPrefix . '_load_after', array($this->_eventObject => $this));
        return parent::_afterLoad();
    }

    /**
     * Add category path filter
     *
     * @param string $regexp
     * @return $this
     */
    public function addPathFilter($regexp)
    {
        $this->addFieldToFilter('path', array('regexp' => $regexp));
        return $this;
    }

    /**
     * Add active category filter
     *
     * @return $this
     */
    public function addIsActiveFilter()
    {
        $this->addFieldToFilter('status', 1);
        $this->_eventManager->dispatch(
            $this->_eventPrefix . '_add_status_filter',
            array($this->_eventObject => $this)
        );
        return $this;
    }


    /**
     * Add section path filter
     *
     * @param array|string $paths
     * @return $this
     */
    public function addPathsFilter($paths)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        $write = $this->getResource()->getWriteConnection();
        $cond = array();
        foreach ($paths as $path) {
            $cond[] = $write->quoteInto('e.path LIKE ?', "{$path}%");
        }
        if ($cond) {
            $this->getSelect()->where(join(' OR ', $cond));
        }
        return $this;
    }

    /**
     * Add category level filter
     *
     * @param int|string $level
     * @return $this
     */
    public function addLevelFilter($level)
    {
        $this->addFieldToFilter('level', array('lteq' => $level));
        return $this;
    }

    /**
     * Add root category filter
     *
     * @return $this
     */
    public function addRootLevelFilter()
    {
        $this->addFieldToFilter('path', array('neq' => '0'));
        $this->addLevelFilter(1);
        return $this;
    }

    /**
     * Add order field
     *
     * @param string $field
     * @return $this
     */
    public function addOrderField($field)
    {
        $this->setOrder($field, self::SORT_ORDER_ASC);
        return $this;
    }
    /**
     * @access public
     * @param $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = array($store->getId());
        }
        if (!is_array($store)) {
            $store = array($store);
        }
        if ($withAdmin) {
            $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }
        $this->addFilter('store', array('in' => $store), 'public');
        return $this;
    }
    protected function _renderFiltersBefore() {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('sample_news_section_store')),
                'main_table.entity_id = store_table.section_id',
                array()
            )->group('main_table.entity_id');
        }
        parent::_renderFiltersBefore();
    }
    public function getSelectCountSql() {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Zend_Db_Select::GROUP);
        return $countSelect;
    }
    public function addCategoryFilter($category) {
        if ($category instanceof \Magento\Catalog\Model\Category){
            $category = $category->getId();
        }
        if (!isset($this->_joinedFields['category'])){
            $this->getSelect()->join(
                array('related_category' => $this->getTable('sample_news_section_category')),
                'related_category.section_id = main_table.entity_id',
                array('position')
            );

            $this->getSelect()->where('related_category.category_id = ?', $category);
            $this->_joinedFields['category'] = true;
        }
        return $this;
    }
    public function addProductFilter($product) {
        if ($product instanceof \Magento\Catalog\Model\Product){
            $product = $product->getId();
        }
        if (!isset($this->_joinedFields['product'])){
            $this->getSelect()->join(
                array('related_product' => $this->getTable('sample_news_section_product')),
                'related_product.section_id = main_table.entity_id',
                array('position')
            );

            $this->getSelect()->where('related_product.product_id = ?', $product);
            $this->_joinedFields['product'] = true;
        }
        return $this;
    }
}
