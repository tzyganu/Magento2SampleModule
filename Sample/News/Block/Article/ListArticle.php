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
namespace Sample\News\Block\Article;


class ListArticle extends \Magento\Framework\View\Element\Template {
    /**
     * @var \Sample\News\Model\Resource\Article\CollectionFactory
     */
    protected $_articleCollectionFactory;
    /**
     * @var \Magento\Framework\UrlFactory
     */
    protected $_urlFactory;


    /**
     * @param \Sample\News\Model\Resource\Article\CollectionFactory $articleCollectionFactory
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Sample\News\Model\Resource\Article\CollectionFactory $articleCollectionFactory,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->_articleCollectionFactory = $articleCollectionFactory;
        $this->_urlFactory = $urlFactory;
        parent::__construct($context, $data);
    }

    /**
     * load the articles
     */
    protected  function _construct() {
        parent::_construct();
        $articles = $this->_articleCollectionFactory->create()->addFieldToSelect('*')
            ->addFieldToFilter('status', 1)
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('title','desc');
        $this->setArticles($articles);
    }

    /**
     * @return bool
     */
    public function isRssEnabled() {
        return
            $this->_scopeConfig->getValue('rss/config/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) &&
            $this->_scopeConfig->getValue('sample_news/article/rss', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'sample_news.article.list.pager')
            ->setCollection($this->getArticles());
        $this->setChild('pager', $pager);
        $this->getArticles()->load();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($this->isRssEnabled()) {
            $title = __('Articles RSS Feed');
            $headBlock->addRss($title, $this->getRssLink());
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    /**
     * @return string
     */
    public function getRssLink() {
        return $this->_urlBuilder->getUrl('sample_news/article/rss', array('store' => $this->_storeManager->getStore()->getId()));
    }
}
