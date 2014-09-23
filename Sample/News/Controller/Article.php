<?php
namespace Sample\News\Controller;
class Article extends \Magento\Framework\App\Action\Action {
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var \Sample\News\Helper\Article
     */
    protected $_articleHelper;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Sample\News\Helper\Article $articleHelper
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Sample\News\Helper\Article $articleHelper,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_articleHelper = $articleHelper;
        parent::__construct($context);
    }
}