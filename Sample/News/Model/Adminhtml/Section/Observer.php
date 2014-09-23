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
namespace Sample\News\Model\Adminhtml\Section;
class Observer {
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry    = null;
    /**
     * @var \Magento\Framework\UrlInterface|null
     */
    protected $_urlBuilder      = null;
    /**
     * @var \Magento\Backend\Helper\Js|null
     */
    protected $_jsHelper        = null;
    /**
     * @var \Magento\Backend\App\Action\Context|null
     */
    protected $_context         = null;
    /**
     * @var null|\Sample\News\Model\Resource\Article
     */
    protected $_sectionResource = null;

    /**
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Backend\Helper\Js $jsHelper
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Sample\News\Model\Resource\Section $sectionResource
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Backend\App\Action\Context $context,
        \Sample\News\Model\Resource\Section $sectionResource
    ) {
        $this->_coreRegistry    = $coreRegistry;
        $this->_urlBuilder      = $urlBuilder;
        $this->_jsHelper        = $jsHelper;
        $this->_context         = $context;
        $this->_sectionResource = $sectionResource;
    }


    /**
     * save product data
     * @access public
     * @param $observer
     * @return $this
     */
    public function saveProductData($observer){
        $post = $this->_context->getRequest()->getPost('sections_ids', -1);
        if ($post != '-1') {
            $product = $this->_coreRegistry->registry('product');
            $this->_sectionResource->saveSectionProductRelation($product, $post);
        }
        return $this;
    }

    /**
     * @access public
     * @param $observer
     * @return $this
     */
    public function addCategoryTab($observer){
        $tabs = $observer->getEvent()->getTabs();
        $tabs->addTab('sections', array(
            'label' => __('Sections'),
            'content' => $tabs->getLayout()->createBlock(
                'Sample\News\Block\Adminhtml\Catalog\Category\Tab\Section',
                'sample_news.category.sections'
            )->toHtml()
        ));
        return $this;
    }

    /**
     * save category data
     * @param $observer
     * @return $this
     */
    public function saveCategoryData($observer) {
        $post = $this->_context->getRequest()->getPost('sample_news_sections_ids', -1);
        if ($post != '-1') {
            $category = $this->_coreRegistry->registry('category');
            $this->_sectionResource->saveSectionCategoryRelation($category, $post);
        }
        return $this;
    }
}
