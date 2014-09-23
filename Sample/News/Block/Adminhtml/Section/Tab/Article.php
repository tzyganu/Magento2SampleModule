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
namespace Sample\News\Block\Adminhtml\Section\Tab;
class Article
    extends \Magento\Backend\Block\Widget\Grid\Extended {
    protected $_articleFactory;
    protected $_coreRegistry;

    /**
     * @param \Sample\News\Model\ArticleFactory $articleFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Sample\News\Model\ArticleFactory $articleFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = array()
    ) {
        $this->_articleFactory = $articleFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct(){
        parent::_construct();
        $this->setId('article_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getSection()->getId()) {
            $this->setDefaultFilter(array('in_articles'=>1));
        }
    }

    /**
     * prepare the collection
     * @return $this
     */
    protected function _prepareCollection() {
        $collection = $this->_articleFactory->create()->getResourceCollection();
        if ($this->getSection()->getId()){
            $constraint = 'related.section_id='.$this->getSection()->getId();
        }
        else{
            $constraint = 'related.section_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('sample_news_article_section')),
            'related.article_id=main_table.entity_id AND '.$constraint,
            array('position'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction(){
        return $this;
    }

    /**
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
        //TODO: add status column
        $this->addColumn('position', array(
            'header'=> __('Position'),
            'name'  => 'position',
            'width' => 60,
            'type'  => 'number',
            'validate_class'=> 'validate-number',
            'index' => 'position',
            'editable'  => true,
        ));
        return $this;
    }
    /**
     * Retrieve selected articles
     * @return array
     */
    protected function _getSelectedArticles(){
        $articles = $this->getRequest()->getPost('selected_articles');
        if (!is_array($articles)) {
            $articles = $this->getSection()->getArticlesPosition();
            return array_keys($articles);
        }
        return $articles;
    }
    /**
     * Retrieve selected articles
     * @return array
     */
    public function getSelectedArticles() {
        $articles = array();
        $selected = $this->getSection()->getSelectedArticles();
        if (!is_array($selected)){
            $selected = array();
        }
        foreach ($selected as $article) {
            $articles[$article->getId()] = array('position' => $article->getPosition());
        }
        return $articles;
    }
    /**
     * @access public
     * @param \Sample\News\Model\Article $item
     * @return string
     */
    public function getRowUrl($item){
        return '#';
    }
    /**
     * get grid url
     * @return string
     */
    public function getGridUrl(){
        return $this->getUrl('*/*/articlesGrid', array(
            'id'=>$this->getSection()->getId()
        ));
    }

    /**
     * @return \Sample\News\Model\Section
     */
    public function getSection(){
        return $this->_coreRegistry->registry('sample_news_section');
    }

    /**
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
