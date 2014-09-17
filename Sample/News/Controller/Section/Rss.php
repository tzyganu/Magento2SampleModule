<?php
namespace Sample\News\Controller\Section;
class Rss extends \Sample\News\Controller\Section {
    protected $_scopeConfig;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Sample\News\Helper\Section $sectionHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $storeManager, $coreRegistry, $sectionHelper);
    }
    protected function _isEnabled() {
        return
            $this->_scopeConfig->getValue('rss/config/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) &&
            $this->_scopeConfig->getValue('sample_news/section/rss', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function execute() {
        if ($this->_isEnabled()) {
            $this->getResponse()->setHeader('Content-Type', 'text/xml; charset=UTF-8');
            $this->_view->loadLayout(false);
            $this->_view->renderLayout();
        }
        else {
            $this->_forward('nofeed', 'index', 'rss');
        }
    }
}