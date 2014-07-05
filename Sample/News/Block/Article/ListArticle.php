<?php
namespace Sample\News\Block\Article;

use Sample\News\Model\Resource\Article\CollectionFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class ListArticle extends Template {
    protected $_articleCollectionFactory;
    protected $_urlFactory;
    protected $_articles;
    public function __construct(
        Context $context,
        CollectionFactory $articleCollectionFactory,
        UrlFactory $urlFactory,
        array $data = array()
    ) {
        $this->_articleCollectionFactory = $articleCollectionFactory;
        $this->_urlFactory = $urlFactory;
        parent::__construct($context, $data);
    }

    protected  function _construct() {
        parent::_construct();
        $articles = $this->_articleCollectionFactory->create()->addFieldToSelect('*')
            ->addFieldToFilter('status', 1)
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('title','desc');
        $this->setArticles($articles);
    }
    public function isRssCatalogEnable() {
        $this->_scopeConfig->getValue('sample_news/article/rss', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'sample_news.article.list.pager'
        )->setCollection(
                $this->getArticles()
            );
        $this->setChild('pager', $pager);
        $this->getArticles()->load();
        return $this;
    }

    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }
}