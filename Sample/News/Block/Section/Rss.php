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
namespace Sample\News\Block\Section;
class Rss extends \Magento\Rss\Block\AbstractBlock {
    /**
     * @var \Sample\News\Helper\Article
     */
    protected $_sectionHelper;
    /**
     * @var \Magento\Rss\Model\RssFactory
     */
    protected $_rssFactory;
    /**
     * @var \Sample\News\Model\Resource\Article\CollectionFactory
     */
    protected $_sectionCollectionFactory;

    /**
     * @param \Sample\News\Helper\Article $articleHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Sample\News\Model\Resource\Article\CollectionFactory $articleCollectionFactory
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rss\Model\RssFactory $rssFactory
     * @param array $data
     */
    public function __construct(
        \Sample\News\Helper\Article $sectionHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Sample\News\Model\Resource\Section\CollectionFactory $sectionCollectionFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rss\Model\RssFactory $rssFactory,
        array $data = array()
    ) {
        $this->_sectionHelper = $sectionHelper;
        $this->_rssFactory = $rssFactory;
        $this->_sectionCollectionFactory = $sectionCollectionFactory;
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
            array('title' => __('Sections'), 'description' => __('Sections'), 'link' => $this->_sectionHelper->getArticlesUrl(), 'charset' => 'UTF-8')
        );

        $_collection = $this->_sectionCollectionFactory->create();
        $_collection->addFieldToFilter('status', 1);
        $_collection->addFieldToFilter('in_rss', 1);
        $_collection->load();

        if ($_collection->getSize() > 0) {
            $args = array('rssObj' => $rssModel);
            foreach ($_collection as $_section) {
                $args['section'] = $_section;
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
        /** @var $section \Sample\News\Model\Section */
        $section = $args['section'];
        $section->setAllowedInRss(true);
        $this->_eventManager->dispatch('rss_sample_news_section_xml_callback', $args);

        if (!$section->getAllowedInRss()) {
            return;
        }

        $description = '<table><tr>' .
            '<td><a href="' .
            $section->getSectionUrl() .
            '">';
        $description .= '</td></tr></table>';
        /** @var $rssObj \Magento\Rss\Model\Rss */
        $rssObj = $args['rssObj'];
        $data = array(
            'title' => $section->getName(),
            'link' => $section->getSectionUrl(),
            'description' => $description
        );
        $rssObj->_addEntry($data);
    }
}
