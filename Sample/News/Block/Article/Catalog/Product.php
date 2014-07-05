<?php
namespace Sample\News\Block\Article\Catalog;

class Product extends \Magento\Framework\View\Element\Template {
    protected $_coreRegistry = null;
    protected $_productVisibility = null;
    protected $_storeManager = null;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_productVisibility = $productVisibility;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }
    public function getArticle(){
        return $this->_coreRegistry->registry('current_article');
    }
    public function getProductCollection() {
        $collection = $this->getArticle()->getSelectedProductsCollection()
            ->setStore($this->_storeManager->getStore())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->setVisibility($this->_productVisibility->getVisibleInCatalogIds());
        $collection->getSelect()->order('position');
//        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
//        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        return $collection;
    }
}