<?php
namespace Sample\News\Model\Author;

use Magento\Framework\UrlInterface;
use Sample\News\Model\Author;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Url
{
    const LIST_URL_CONFIG_PATH = 'sample_news/author/list_url';
    const URL_PREFIX_CONFIG_PATH = 'sample_news/author/url_prefix';
    const URL_SUFFIX_CONFIG_PATH = 'sample_news/author/url_suffix';
    /**
     * url builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    protected $scopeConfig;

    /**
     * @param UrlInterface $urlBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    public function getListUrl()
    {
        $sefUrl = $this->scopeConfig->getValue(self::LIST_URL_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
        if ($sefUrl) {
            return $this->urlBuilder->getUrl('', ['_direct' => $sefUrl]);
        }
        return $this->urlBuilder->getUrl('sample_news/author/index');
    }

    /**
     * @param Author $author
     * @return string
     */
    public function getAuthorUrl(Author $author)
    {
        if ($urlKey = $author->getUrlKey()) {
            $prefix = $this->scopeConfig->getValue(
                self::URL_PREFIX_CONFIG_PATH,
                ScopeInterface::SCOPE_STORE
            );
            $suffix = $this->scopeConfig->getValue(
                self::URL_SUFFIX_CONFIG_PATH,
                ScopeInterface::SCOPE_STORE
            );
            $path = (($prefix) ? $prefix . '/' : '').
                $urlKey .
                (($suffix) ? '.'. $suffix : '');
            return $this->urlBuilder->getUrl('', ['_direct'=>$path]);
        }
        return $this->urlBuilder->getUrl('sample_news/author/view', ['id' => $author->getId()]);
    }
}
