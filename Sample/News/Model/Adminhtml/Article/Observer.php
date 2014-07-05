<?php
namespace Sample\News\Model\Adminhtml\Article;
class Observer {
    protected $_coreRegistry    = null;
    protected $_urlBuilder      = null;
    protected $_jsHelper        = null;
    protected $_context         = null;
    protected $_articleResource = null;
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Backend\App\Action\Context $context,
        \Sample\News\Model\Resource\Article $articleResource
    ) {
        $this->_coreRegistry    = $coreRegistry;
        $this->_urlBuilder      = $urlBuilder;
        $this->_jsHelper        = $jsHelper;
        $this->_context         = $context;
        $this->_articleResource = $articleResource;
    }
    public function addArticleTab($observer) {
        $block = $observer->getEvent()->getBlock();
        //TODO: find a better way to add the tab.
        if ($block instanceof \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs){
            $block->addTab('sample_news_articles', array(
                'label' => __('Articles'),
                'url'   => $this->_urlBuilder->getUrl('sample_news/catalog_product/articles', array('_current' => true)),
                'class' => 'ajax',
            ));
        }
        return $this;
    }
    public function saveProductData($observer){
        $post = $this->_context->getRequest()->getPost('articles', -1);
        if ($post != '-1') {
            $post = $this->_jsHelper->decodeGridSerializedInput($post);
            $product = $this->_coreRegistry->registry('product');
            $this->_articleResource->saveArticleProductRelation($product, $post);
        }
        return $this;
    }
}