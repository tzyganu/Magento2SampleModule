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
namespace Sample\News\Block\Adminhtml\Catalog\Product\Edit\Tab;

class Article
    extends \Magento\Backend\Block\Widget\Grid\Extended
    implements \Magento\Backend\Block\Widget\Tab\TabInterface{
    /**
     * @var null|\Sample\News\Model\ArticleFactory
     */
    protected $_articleFactory = null;
    /**
     * @var null|\Sample\News\Helper\Product
     */
    protected $_productHelper = null;
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_registry = null;

    /**
     * @access public
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Sample\News\Model\ArticleFactory $articleFactory
     * @param \Sample\News\Helper\Product $productHelper
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Sample\News\Model\ArticleFactory $articleFactory,
        \Sample\News\Helper\Product $productHelper,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_articleFactory = $articleFactory;
        $this->_productHelper = $productHelper;
        $this->_registry = $registry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @access public
     */
    public function _construct() {
        parent::_construct();
        $this->setId('article_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getProduct()->getId()) {
            $this->setDefaultFilter(array('in_articles'=>1));
        }
    }

    /**
     * prepare collection
     * @access protected
     * @return $this
     */
    protected function _prepareCollection() {
        $collection = $this->_articleFactory->create()->getResourceCollection();
        if ($this->getProduct()->getId()){
            $constraint = 'related.product_id='.$this->getProduct()->getId();
        }
        else{
            $constraint = 'related.product_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related'=>$collection->getTable('sample_news_article_product')),
            'related.article_id=main_table.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * no mass action here
     * @access protected
     * @return $this
     */
    protected function _prepareMassaction(){
        return $this;
    }

    /**
     * prepare columns
     * @access protected
     * @return $this
     */
    protected function _prepareColumns(){
        $this->addColumn('in_articles', array(
            'header_css_class'  => 'a-center',
            'type'  => 'checkbox',
            'name'  => 'in_articles',
            'values'=> $this->_getSelectedArticles(),
            'align' => 'center',
            'index' => 'entity_id'
        ));
        $this->addColumn('title', array(
            'header'=> __('Title'),
            'align' => 'left',
            'index' => 'title',
        ));
        $this->addColumn('position', array(
            'header'        => __('Position'),
            'name'          => 'position',
            'width'         => 60,
            'type'        => 'number',
            'validate_class'=> 'validate-number',
            'index'         => 'position',
            'editable'      => true,
        ));
        return parent::_prepareColumns();
    }
    protected function _getSelectedArticles(){
        $articles = $this->getProductArticles();
        if (!is_array($articles)) {
            $articles = array_keys($this->getSelectedArticles());
        }
        return $articles;
    }

    /**
     * get selected articles
     * @access public
     * @return array
     */
    public function getSelectedArticles() {
        $articles = array();
        $selected = $this->_productHelper->getSelectedArticles($this->getProduct());
        if (!is_array($selected)){
            $selected = array();
        }
        foreach ($selected as $article) {
            $articles[$article->getId()] = array('position' => $article->getPosition());
        }
        return $articles;
    }

    /**
     * get row url
     * @access public
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $item
     * @return string
     */
    public function getRowUrl($item){
        return '#';
    }

    /**
     * get grid url
     * @access public
     * @return string
     */
    public function getGridUrl(){
        return $this->_urlBuilder->getUrl('*/*/articlesGrid', array(
            'id'=>$this->getProduct()->getId()
        ));
    }

    /**
     * get current product
     * @access public
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct(){
        return $this->_registry->registry('current_product');
    }

    /**
     * @access protected
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column){
        if ($column->getId() == 'in_articles') {
            $articleIds = $this->_getSelectedArticles();
                if (empty($articleIds)) {
                    $articleIds = 0;
                }
                if ($column->getFilter()->getValue()) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$articleIds));
                }
                else {
                    if($articleIds) {
                        $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$articleIds));
                    }
                }
            }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTabLabel() {
        return __('Associated Articles');
    }

    /**
     * @return bool
     */
    public function isHidden() {
        return false;
    }

    /**
     * @return string
     */
    public function getTabTitle() {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab() {
        return true;
    }

    /**
     * @return string
     */
    public function getTabUrl() {
        return $this->getUrl('sample_news/catalog_product/articles', array('_current' => true));
    }
    public function getTabClass() {
        return 'ajax only';
    }
}
