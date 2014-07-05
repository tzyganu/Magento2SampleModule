<?php
namespace Sample\News\Block\Adminhtml\Article\Edit\Tab;
class Product
    extends \Magento\Backend\Block\Widget\Grid\Extended {

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
     * @author Ultimate Module Creator
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

    protected function _prepareMassaction(){
        return $this;
    }

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
    }
    /**
     * Retrieve selected products
     * @access protected
     * @return array
     * @author Ultimate Module Creator
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
     * @access protected
     * @return array
     * @author Ultimate Module Creator
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
     * @author Ultimate Module Creator
     */
    public function getGridUrl(){
        return $this->getUrl('*/*/productsGrid', array(
            'id'=>$this->getArticle()->getId()
        ));
    }

    /**
     * @return mixed
     */
    public function getArticle(){
        return $this->_coreRegistry->registry('sample_news_article');
//        return Mage::registry('current_article');
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column){
        // Set custom filter for in product flag
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
}
