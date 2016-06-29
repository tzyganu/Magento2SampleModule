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
use Sample\News\Model\Author;

class Url
{
    /**
     * @var string
     */
    const LIST_URL_CONFIG_PATH      = 'sample_news/author/list_url';
    /**
     * @var string
     */
    const URL_PREFIX_CONFIG_PATH    = 'sample_news/author/url_prefix';
    /**
     * @var string
     */
    const URL_SUFFIX_CONFIG_PATH    = 'sample_news/author/url_suffix';
    /**
     * url builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param UrlInterface $urlBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
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
