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
namespace Sample\News\Model\Adminhtml\Article;
class Observer {
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry;
    /**
     * @var \Magento\Framework\UrlInterface|null
     */
    protected $_urlBuilder;
    /**
     * @var \Magento\Backend\Helper\Js|null
     */
    protected $_jsHelper;
    /**
     * @var \Magento\Backend\App\Action\Context|null
     */
    protected $_context;
    /**
     * @var \Sample\News\Model\Resource\Article
     */
    protected $_articleResource;

    /**
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Backend\Helper\Js $jsHelper
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Sample\News\Model\Resource\Article $articleResource
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Backend\App\Action\Context $context,
        \Sample\News\Model\Resource\Article $articleResource
    ) {
        $this->_coreRegistry    = $coreRegistry;
        $this->_urlBuilder      = $urlBuilder;
        $this->_jsHelper        = $jsHelper;
        $this->_context         = $context;
        $this->_articleResource = $articleResource;
    }


    /**
     * save product data
     * @param $observer
     * @return $this
     */
    public function saveProductData($observer){
        $post = $this->_context->getRequest()->getPost('articles', -1);
        if ($post != '-1') {
            $post = $this->_jsHelper->decodeGridSerializedInput($post);
            $product = $this->_coreRegistry->registry('product');
            $this->_articleResource->saveArticleProductRelation($product, $post);
        }
        return $this;
    }

    /**
     * @param $observer
     * @return $this
     */
    public function addCategoryTab($observer){
        $tabs = $observer->getEvent()->getTabs();
        $container = $tabs->getLayout()->createBlock(
            'Magento\Backend\Block\Template',
            'category.article.grid.wrapper'
        );
        /** @var \Magento\Backend\Block\Template  $container */
        $container->setTemplate('Sample_News::catalog/category/article.phtml');
        $tab = $tabs->getLayout()->createBlock(
                'Sample\News\Block\Adminhtml\Catalog\Category\Tab\Article',
                'category.sample_news.article.grid'
        );

        $container->setChild('grid', $tab);
        $content = $container->toHtml();
        $tabs->addTab('sample_news_articles', array(
            'label'     => __('Articles'),
            'content'   => $content,
        ));
        return $this;
    }

    /**
     * save category data
     * @param $observer
     * @return $this
     */
    public function saveCategoryData($observer) {
        $post = $this->_context->getRequest()->getPost('category_sample_news_articles', -1);
        if ($post != '-1') {
            $post = json_decode($post, true);
            $category = $this->_coreRegistry->registry('category');
            $this->_articleResource->saveArticleCategoryRelation($category, $post);
        }
        return $this;
    }
}
