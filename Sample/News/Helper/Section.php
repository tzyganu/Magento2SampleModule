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
class Section
    extends \Magento\Framework\App\Helper\AbstractHelper {
    /**
     * use breadcrumbs path
     */
    const BREADCRUMBS_CONFIG_PATH = 'sample_news/section/breadcrumbs';
    /**
     * list meta title path
     */
    const LIST_META_TITLE_CONFIG_PATH = 'sample_news/section/meta_title';
    /**
     * list meta description path
     */
    const LIST_META_DESCRIPTION_CONFIG_PATH = 'sample_news/section/meta_description';
    /**
     * list meta keywords path
     */
    const LIST_META_KEYWORDS_CONFIG_PATH = 'sample_news/section/meta_keywords';
    /**
     * list url key path
     */
    const LIST_PATH = 'sample_news/section/list_url';
    protected $_scopeConfig;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    const ROOT_SECTION_ID = 1;

    /**
     * @return int
     */
    public function getRootSectionId() {
        return self::ROOT_SECTION_ID;
    }
    public function getSectionsUrl() {
        if ($listKey = $this->_scopeConfig->getValue(self::LIST_PATH)) {
            return $this->_getUrl('', array('_direct' => $listKey));
        }
        return $this->_getUrl('sample_news/section/index');
    }
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