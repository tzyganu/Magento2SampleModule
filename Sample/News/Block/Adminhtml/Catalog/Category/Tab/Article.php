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
namespace Sample\News\Block\Adminhtml\Catalog\Category\Tab;

class Article
    extends \Magento\Backend\Block\Widget\Grid\Extended {
    /**
     * @var \Sample\News\Model\ArticleFactory
     */
    protected $_articleFactory;
    /**
     * @var \Sample\News\Helper\Category
     */
    protected $_categoryHelper;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @access public
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Sample\News\Model\ArticleFactory $articleFactory
     * @param \Sample\News\Helper\Category $categoryHelper
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Sample\News\Model\ArticleFactory $articleFactory,
        \Sample\News\Helper\Category $categoryHelper,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_articleFactory = $articleFactory;
        $this->_categoryHelper = $categoryHelper;
        $this->_registry = $registry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @access public
     */
    public function _construct() {
        parent::_construct();
        $this->setId('catalog_category_sample_news_article');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_articles'=>1));
        }
    }

    /**
     * get current category
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory() {
        return $this->_registry->registry('current_category');
    }

    /**
     * prepare collection
     * @access protected
     * @return $this
     */
    protected function _prepareCollection() {
        $collection = $this->_articleFactory->create()->getResourceCollection();
        if ($this->getCategory()->getId()){
            $constraint = 'related.category_id='.$this->getCategory()->getId();
        }
        else{
            $constraint = 'related.category_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('sample_news_article_category')),
            'related.article_id=main_table.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * prepare columns
     * @access protected
     * @return $this
     */
    protected function _prepareColumns() {
        $this->addColumn('in_articles', array(
            'header_css_class'  => 'a-center',
            'type'  => 'checkbox',
            'name'  => 'in_articles',
            'values'=> $this->_getSelectedArticles(),
            'align' => 'center',
            'index' => 'entity_id'
        ));
        $this->addColumn('entity_id', array(
            'header'=> __('Id'),
            'type'  => 'number',
            'align' => 'left',
            'index' => 'entity_id',
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

    /**
     * get selected articles
     * @access protected
     * @return array
     */
    protected function _getSelectedArticles(){
        $articles = $this->getCategoryArticles();
        if (!is_array($articles)) {
            $articles = array_keys($this->getSelectedArticles());
        }
        return $articles;
    }

    /**
     * @access public
     * @return array
     */
    public function getSelectedArticles() {
        $articles = array();
        $selected = $this->_categoryHelper->getSelectedArticles($this->getCategory());
        if (!is_array($selected)){
            $selected = array();
        }
        foreach ($selected as $article) {
            $articles[$article->getId()] = $article->getPosition();
        }
        return $articles;
    }

    /**
     * get row URL
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
        return $this->getUrl('sample_news/catalog_category/articlesGrid', array(
            'id'=>$this->getCategory()->getId()
        ));
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
}
