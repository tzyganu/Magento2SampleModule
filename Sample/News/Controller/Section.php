<?php
namespace Sample\News\Controller;
class Section extends \Magento\Framework\App\Action\Action {
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var \Sample\News\Helper\Article
     */
    protected $_sectionHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Sample\News\Helper\Section $sectionHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Sample\News\Helper\Section $sectionHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_sectionHelper = $sectionHelper;
        parent::__construct($context);
    }
}