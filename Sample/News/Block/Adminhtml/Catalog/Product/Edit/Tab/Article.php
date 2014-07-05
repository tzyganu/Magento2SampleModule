<?php
namespace Sample\News\Block\Adminhtml\Catalog\Product\Edit\Tab;

class Article extends \Magento\Backend\Block\Widget\Grid\Extended {
    protected $_articleFactory = null;
    protected $_productHelper = null;
    protected $_registry = null;
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

    protected function _prepareCollection() {
        $collection = $this->_articleFactory->create()->getCollection();
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
    protected function _prepareMassaction(){
        return $this;
    }
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

    public function getSelectedArticles() {
        $articles = array();
        //used helper here in order not to override the product model
        $selected = $this->_productHelper->getSelectedArticles($this->getProduct());
        if (!is_array($selected)){
            $selected = array();
        }
        foreach ($selected as $article) {
            $articles[$article->getId()] = array('position' => $article->getPosition());
        }
        return $articles;
    }

    public function getRowUrl($item){
        return '#';
    }

    public function getGridUrl(){
        return $this->_urlBuilder->getUrl('*/*/articlesGrid', array(
            'id'=>$this->getProduct()->getId()
        ));
    }
    public function getProduct(){
        return $this->_registry->registry('current_product');
    }
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