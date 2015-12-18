<?php
namespace Sample\News\Controller\Author;

use Magento\Rss\Controller\Feed\Index;
use Magento\Framework\App\Action\Context;
use Sample\News\Model\Author\Rss as RssModel;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;

class Rss extends Index
{
    protected $rssModel;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultLayoutFactory;
    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param RssModel $rssModel
     * @param Context $context
     * @param \Magento\Rss\Model\RssManager $rssManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Rss\Model\RssFactory $rssFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement
     * @param \Magento\Framework\HTTP\Authentication $httpAuthentication
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        RssModel $rssModel,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Rss\Model\RssManager $rssManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Rss\Model\RssFactory $rssFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\HTTP\Authentication $httpAuthentication,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->rssModel = $rssModel;
        $this->rssManager = $rssManager;
        $this->scopeConfig = $scopeConfig;
        $this->rssFactory = $rssFactory;
        $this->customerSession = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->httpAuthentication = $httpAuthentication;
        $this->logger = $logger;
        parent::__construct(
            $context,
            $rssManager,
            $scopeConfig,
            $rssFactory,
            $customerSession,
            $customerAccountManagement,
            $httpAuthentication,
            $logger
        );
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->getRequest()->setParam('type', 'authors');
        parent::execute();
    }
}
