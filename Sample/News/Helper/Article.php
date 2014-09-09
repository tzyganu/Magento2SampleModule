<?php
/**
 * Sample_News extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Sample
 * @package        Sample_News
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Sample\News\Helper;
class Article
    extends \Magento\Framework\App\Helper\AbstractHelper {
    /**
     * use breadcrumbs path
     */
    const BREADCRUMBS_CONFIG_PATH = 'sample_news/article/breadcrumbs';
    /**
     * list meta title path
     */
    const LIST_META_TITLE_CONFIG_PATH = 'sample_news/article/meta_title';
    /**
     * list meta description path
     */
    const LIST_META_DESCRIPTION_CONFIG_PATH = 'sample_news/article/meta_description';
    /**
     * list meta keywords path
     */
    const LIST_META_KEYWORDS_CONFIG_PATH = 'sample_news/article/meta_keywords';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @access public
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * get article page url
     * @access public
     * @return string
     */
    public function getArticlesUrl() {
        return $this->_getUrl('sample_news/article/index');
    }

    /**
     * @access public
     * @return bool
     */
    public function getUseBreadcrumbs() {
        return $this->_scopeConfig->isSetFlag(self::BREADCRUMBS_CONFIG_PATH);
    }

    /**
     * @access public
     * @return string
     */
    public function getListMetaTitle() {
        return $this->_scopeConfig->getValue(self::LIST_META_TITLE_CONFIG_PATH);
    }

    /**
     * @access public
     * @return mixed
     */
    public function getListMetaDescription() {
        return $this->_scopeConfig->getValue(self::LIST_META_DESCRIPTION_CONFIG_PATH);
    }

    /**
     * @access public
     * @return mixed
     */
    public function getListMetaKeywords() {
        return $this->_scopeConfig->getValue(self::LIST_META_KEYWORDS_CONFIG_PATH);
    }
}
