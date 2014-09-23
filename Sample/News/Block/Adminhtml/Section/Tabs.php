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
namespace Sample\News\Block\Adminhtml\Section;

class Tabs
    extends \Magento\Backend\Block\Widget\Tabs {
    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::widget/tabshoriz.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;
    /**
     * @var null|\Sample\News\Helper\Section
     */
    protected $_helperSection = null;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Sample\News\Helper\Section $helperSection
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Sample\News\Helper\Section $helperSection,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_helperSection = $helperSection;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Initialize Tabs
     *
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('section_info_tabs');
        $this->setDestElementId('section_tab_content');
        $this->setTitle(__('Section Data'));
    }

    /**
     * @return \Sample\News\Model\Data
     */
    public function getSection() {
        return $this->_coreRegistry->registry('sample_news_section');
    }

    /**
     * Prepare Layout Content
     * @return $this
     */
    protected function _prepareLayout() {
        $this->addTab('section', array(
            'label' => __('Section Info'),
            'content' => $this->getLayout()->createBlock(
                'Sample\News\Block\Adminhtml\Section\Tab\Section',
                'sample_news.section.info'
            )->toHtml()
        ));
        $this->addTab('meta', array(
            'label' => __('Meta'),
            'content' => $this->getLayout()->createBlock(
                'Sample\News\Block\Adminhtml\Section\Tab\Meta',
                'sample_news.section.meta'
            )->toHtml()
        ));
        $this->addTab('stores', array(
            'label' => __('Stores'),
            'content' => $this->getLayout()->createBlock(
                'Sample\News\Block\Adminhtml\Section\Tab\Stores',
                'sample_news.section.stores'
            )->toHtml()
        ));

        $this->addTab('products', array(
            'label' => __('Products'),
            'content' => $this->getLayout()->createBlock(
                'Sample\News\Block\Adminhtml\Section\Tab\Product',
                'sample_news.section.products'
            )->toHtml()
        ));
        $this->addTab('categories', array(
            'label' => __('Categories'),
            'content' => $this->getLayout()->createBlock(
                'Sample\News\Block\Adminhtml\Section\Tab\Category',
                'sample_news.section.categories'
            )->toHtml()
        ));
        $this->addTab('articles', array(
            'label' => __('Articles'),
            'content' => $this->getLayout()->createBlock(
                'Sample\News\Block\Adminhtml\Section\Tab\Article',
                'sample_news.section.articles'
            )->toHtml()
        ));

        // dispatch event add custom tabs
        $this->_eventManager->dispatch('adminhtml_sample_news_section_tabs', array('tabs' => $this));

        return parent::_prepareLayout();
    }
}
