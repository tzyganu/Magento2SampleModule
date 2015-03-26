<?php
namespace Sample\News\Model\Author;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Rss
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $urlBuilder;
    protected $storeManager;

    /**
     * @param UrlInterface $urlBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @return bool
     */
    public function isRssEnabled()
    {
        return
            $this->scopeConfig->getValue('rss/config/active', ScopeInterface::SCOPE_STORE) &&
            $this->scopeConfig->getValue('sample_news/author/rss', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getRssLink()
    {
        return $this->urlBuilder->getUrl(
            'sample_news/author/rss',
            ['store' => $this->storeManager->getStore()->getId()]
        );
    }
}
