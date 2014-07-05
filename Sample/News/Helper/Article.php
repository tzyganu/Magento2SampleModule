<?php
namespace Sample\News\Helper;
class Article extends \Magento\Framework\App\Helper\AbstractHelper {
    const BREADCRUMBS_CONFIG_PATH = 'sample_news/article/breadcrumbs';
    const LIST_META_TITLE_CONFIG_PATH = 'sample_news/article/meta_title';
    const LIST_META_DESCRIPTION_CONFIG_PATH = 'sample_news/article/meta_description';
    const LIST_META_KEYWORDS_CONFIG_PATH = 'sample_news/article/meta_keywords';
    protected $_scopeConfig;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function getArticlesUrl() {
        return $this->_getUrl('sample_news/article/index');
    }
    public function getArticleUrl(\Sample\News\Model\Article $article) {
        $identifier = $article->getIdentifier();
        if ($identifier) {
            return $this->_getUrl('', array('_direct' => $identifier));
        }
        return $this->_getUrl('sample_news/article/view', array('id' => $article->getId()));
    }
    public function getUseBreadcrumbs() {
        return $this->_scopeConfig->isSetFlag(self::BREADCRUMBS_CONFIG_PATH);
    }
    public function getListMetaTitle() {
        return $this->_scopeConfig->getValue(self::LIST_META_TITLE_CONFIG_PATH);
    }
    public function getListMetaDescription() {
        return $this->_scopeConfig->getValue(self::LIST_META_DESCRIPTION_CONFIG_PATH);
    }
    public function getListMetaKeywords() {
        return $this->_scopeConfig->getValue(self::LIST_META_KEYWORDS_CONFIG_PATH);
    }
}
