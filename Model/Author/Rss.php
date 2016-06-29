<?php
/**
 * Sample_News extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Sample
 * @package   Sample_News
 * @copyright 2016 Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru
 */
namespace Sample\News\Model\Author;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Rss
{
    /**
     * @var string
     */
    const RSS_PAGE_URL                  = 'sample_news/author/rss';
    /**
     * @var string
     */
    const AUTHOR_RSS_ACTIVE_CONFIG_PATH = 'sample_news/author/rss';
    /**
     * @var string
     */
    const GLOBAL_RSS_ACTIVE_CONFIG_PATH = 'rss/config/active';
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var StoreManagerInterface
     */
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
    ) {
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
            $this->scopeConfig->getValue(self::GLOBAL_RSS_ACTIVE_CONFIG_PATH, ScopeInterface::SCOPE_STORE) &&
            $this->scopeConfig->getValue(self::AUTHOR_RSS_ACTIVE_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getRssLink()
    {
        return $this->urlBuilder->getUrl(
            self::RSS_PAGE_URL,
            ['store' => $this->storeManager->getStore()->getId()]
        );
    }
}
