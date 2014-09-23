<?php
namespace Sample\News\Controller\Article;
class Rss extends \Sample\News\Controller\Article {
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Sample\News\Helper\Article $articleHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Sample\News\Helper\Article $articleHelper
    ){
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($storeManager, $coreRegistry, $articleHelper, $context);
    }

    /**
     * @return bool
     */
    protected function _isEnabled() {
        return
            $this->_scopeConfig->getValue('rss/config/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) &&
            $this->_scopeConfig->getValue('sample_news/article/rss', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return void
     */
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
