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
namespace Sample\News\Block\Adminhtml\Article\Edit\Tab;
class Product
    extends \Magento\Backend\Block\Widget\Grid\Extended
    implements \Magento\Backend\Block\Widget\Tab\TabInterface {
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;
    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;
    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;
    /**
     * @var  \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @access public
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Framework\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_productFactory = $productFactory;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     * @access protected
     */
    public function _construct(){
        parent::_construct();
        $this->setId('product_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getArticle()->getId()) {
            $this->setDefaultFilter(array('in_products'=>1));
        }
    }

    /**
     * prepare the collection
     * @access protected
     * @return $this
     */
    protected function _prepareCollection() {
        $collection = $this->_productFactory->create()->getCollection();
        $collection->addAttributeToSelect('price');
        $adminStore = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        $collection->joinAttribute('product_name', 'catalog_product/name', 'entity_id', null, 'left', $adminStore);
        if ($this->getArticle()->getId()){
            $constraint = '{{table}}.article_id='.$this->getArticle()->getId();
        }
        else{
            $constraint = '{{table}}.article_id=0';
        }
        $collection->joinField('position',
            'sample_news_article_product',
            'position',
            'product_id=entity_id',
            $constraint,
            'left');
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * @access
     * @return $this
     */
    protected function _prepareMassaction(){
        return $this;
    }

    /**
     * @access protected
     * @return $this
     */
    protected function _prepareColumns(){
        $this->addColumn('in_products', array(
            'header_css_class'  => 'a-center',
            'type'  => 'checkbox',
            'name'  => 'in_products',
            'values'=> $this->_getSelectedProducts(),
            'align' => 'center',
            'index' => 'entity_id'
        ));

        $this->addColumn('product_name', array(
            'header'=> __('Name'),
            'align' => 'left',
            'index' => 'product_name',
        ));
        $this->addColumn('sku', array(
            'header'=> __('SKU'),
            'align' => 'left',
            'index' => 'sku',
        ));
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
     * Retrieve selected products
     * @access protected
     * @return array
     */
    protected function _getSelectedProducts(){
        $products = $this->getArticleProducts();
        if (!is_array($products)) {
            $products = $this->getArticle()->getProductsPosition();
            return array_keys($products);
        }
        return $products;
    }
    /**
     * Retrieve selected products
     * @access public
     * @return array
     */
    public function getSelectedProducts() {
        $products = array();
        $selected = $this->getArticle()->getSelectedProducts();
        if (!is_array($selected)){
            $selected = array();
        }
        foreach ($selected as $product) {
            $products[$product->getId()] = array('position' => $product->getPosition());
        }
        return $products;
    }
    /**
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
        return $this->getUrl('*/*/productsGrid', array(
            'id'=>$this->getArticle()->getId()
        ));
    }

    /**
     * @access public
     * @return mixed
     */
    public function getArticle(){
        return $this->_coreRegistry->registry('sample_news_article');
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column){
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
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
        return __('Associated Products');
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
        return $this->getUrl('sample_news/article/products', array('_current' => true));
    }

    /**
     * @return string
     */
    public function getTabClass() {
        return 'ajax only';
    }
}
