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
class Rss extends \Magento\Rss\Block\AbstractBlock {
    /**
     * @var \Sample\News\Helper\Article
     */
    protected $_articleHelper;
    /**
     * @var \Magento\Rss\Model\RssFactory
     */
    protected $_rssFactory;
    /**
     * @var \Sample\News\Model\Resource\Article\CollectionFactory
     */
    protected $_articleCollectionFactory;

    /**
     * @param \Sample\News\Helper\Article $articleHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Sample\News\Model\Resource\Article\CollectionFactory $articleCollectionFactory
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rss\Model\RssFactory $rssFactory
     * @param array $data
     */
    public function __construct(
        \Sample\News\Helper\Article $articleHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Sample\News\Model\Resource\Article\CollectionFactory $articleCollectionFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rss\Model\RssFactory $rssFactory,
        array $data = array()
    ) {
        $this->_articleHelper = $articleHelper;
        $this->_rssFactory = $rssFactory;
        $this->_articleCollectionFactory = $articleCollectionFactory;
        parent::__construct($context, $httpContext, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml() {
        $storeId = $this->_getStoreId();
        /** @var $rssModel \Magento\Rss\Model\Rss */
        $rssModel = $this->_rssFactory->create();
        $rssModel->_addHeader(
            array('title' => __('Articles'), 'description' => __('Articles'), 'link' => $this->_articleHelper->getArticlesUrl(), 'charset' => 'UTF-8')
        );

        $_collection = $this->_articleCollectionFactory->create();
        $_collection->addFieldToFilter('status', 1);
        $_collection->addFieldToFilter('in_rss', 1);
        $_collection->load();

        if ($_collection->getSize() > 0) {
            $args = array('rssObj' => $rssModel);
            foreach ($_collection as $_article) {
                $args['article'] = $_article;
                $this->addNewItemXmlCallback($args);
            }
        }
        return $rssModel->createRssXml();
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     * @return void
     */
    public function addNewItemXmlCallback($args) {
        /** @var $article \Sample\News\Model\Article */
        $article = $args['article'];
        $article->setAllowedInRss(true);
        $this->_eventManager->dispatch('rss_sample_news_article_xml_callback', $args);

        if (!$article->getAllowedInRss()) {
            return;
        }

        $description = '<table><tr>' .
            '<td><a href="' .
            $article->getArticleUrl() .
            '"></td>' .
            '<td  style="text-decoration:none;">' .
            $article->getContent();
        $description .= '</td></tr></table>';
        /** @var $rssObj \Magento\Rss\Model\Rss */
        $rssObj = $args['rssObj'];
        $data = array(
            'title' => $article->getTitle(),
            'link' => $article->getArticleUrl(),
            'description' => $description
        );
        $rssObj->_addEntry($data);
    }
}
