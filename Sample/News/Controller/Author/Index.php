<?php
namespace Sample\News\Controller\Author;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Sample\News\Model\Author\Rss;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Index extends Action
{
    const META_DESCRIPTION_CONFIG_PATH = 'sample_news/author/meta_description';
    const META_KEYWORDS_CONFIG_PATH = 'sample_news/author/meta_keywords';
    protected $rss;
    protected $scopeConfig;
    protected $storeManager;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Rss $rss
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Rss $rss,
        ScopeConfigInterface $scopeConfig
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->rss = $rss;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Authors'));
        $resultPage->getConfig()->setDescription(
            $this->scopeConfig->getValue(self::META_DESCRIPTION_CONFIG_PATH, ScopeInterface::SCOPE_STORE)
        );
        $resultPage->getConfig()->setKeywords(
            $this->scopeConfig->getValue(self::META_KEYWORDS_CONFIG_PATH, ScopeInterface::SCOPE_STORE)
        );
        return $resultPage;
    }
}
