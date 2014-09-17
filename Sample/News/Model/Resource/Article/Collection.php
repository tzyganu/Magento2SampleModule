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
namespace Sample\News\Model\Resource\Article;

class Collection
    extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection {
    protected $_joinedFields = array();
    /**
     * Define resource model
     * @access protected
     * @return void
     */
    protected function _construct() {
        $this->_init('Sample\News\Model\Article', 'Sample\News\Model\Resource\Article');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Returns pairs block_id - title
     * @access public
     * @return array
     */
    public function toOptionArray() {
        return $this->_toOptionArray('entity_id', 'title');
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

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     * @access pro
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql() {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Zend_Db_Select::GROUP);
        return $countSelect;
    }

    /**
     * Join store relation table if there is store filter
     * @access protected
     * @return void
     */
    protected function _renderFiltersBefore() {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                array('store_table' => $this->getTable('sample_news_article_store')),
                'main_table.entity_id = store_table.article_id',
                array()
            )->group('main_table.entity_id');
        }
        parent::_renderFiltersBefore();
    }

    /**
     * @add product filter
     * @access public
     * @param $product
     * @return $this
     */
    public function addProductFilter($product) {
        if ($product instanceof \Magento\Catalog\Model\Product){
            $product = $product->getId();
        }
        if (!isset($this->_joinedFields['product'])){
            $this->getSelect()->join(
                array('related_product' => $this->getTable('sample_news_article_product')),
                'related_product.article_id = main_table.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_product.product_id = ?', $product);
            $this->_joinedFields['product'] = true;
        }
        return $this;
    }

    /**
     * @access public
     * @param $category
     * @return $this
     */
    public function addCategoryFilter($category) {
        if ($category instanceof \Magento\Catalog\Model\Category){
            $category = $category->getId();
        }
        if (!isset($this->_joinedFields['category'])){
            $this->getSelect()->join(
                array('related_category' => $this->getTable('sample_news_article_category')),
                'related_category.article_id = main_table.entity_id',
                array('position')
            );

            $this->getSelect()->where('related_category.category_id = ?', $category);
            $this->_joinedFields['category'] = true;
        }
        return $this;
    }
}
