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
namespace Sample\News\Controller;

class Article
    extends \Magento\Framework\App\Action\Action {
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var \Sample\News\Helper\Article
     */
    protected $_articleHelper;

    /**
     * @access public
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Sample\News\Helper\Article $articleHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Sample\News\Helper\Article $articleHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_articleHelper = $articleHelper;
        parent::__construct($context);
    }

    /**
     * index action
     * @access public
     */
    public function indexAction() {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $headBlock = $this->_view->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->_articleHelper->getListMetaTitle());
            $headBlock->setKeywords($this->_articleHelper->getListMetaKeywords());
            $headBlock->setDescription($this->_articleHelper->getListMetaDescription());
        }
        $titleBlock = $this->_view->getLayout()->getBlock('page.main.title');
        if ($titleBlock) {
            $titleBlock->setPageTitle(__('Articles'));
        }
        if ($this->_articleHelper->getUseBreadcrumbs() && $breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ])
                ->addCrumb('articles', [
                    'label' => __('Articles')
                ]);
        }
        $this->_view->renderLayout();
    }

    /**
     * $init the article
     * access protected
     * @return bool|\Sample\News\Model\Article
     */
    protected function _initArticle(){
        $articleId   = $this->getRequest()->getParam('id', 0);
        $article     = $this->_objectManager->create('Sample\News\Model\Article')
            ->setStoreId($this->_storeManager->getStore()->getId())
            ->load($articleId);
        if (!$article->getId()){
            return false;
        }
        elseif (!$article->getStatus()){
            return false;
        }
        return $article;
    }

    /**
     * view action
     * @access public
     */
    public function viewAction() {
        $article = $this->_initArticle();
        if (!$article) {
            $this->_forward('no-route');
            return;
        }
        $this->_coreRegistry->register('current_article', $article);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        if ($root = $this->_view->getLayout()->getBlock('root')) {
            $root->addBodyClass('news-article news-article-' . $article->getId());
        }
        if ($this->_articleHelper->getUseBreadcrumbs() && $breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs')){
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->_storeManager->getStore()->getBaseUrl()
            ])
            ->addCrumb('articles',[
                'label' => __('Articles'),
                'title' => __('Articles'),
                'link'  => $this->_articleHelper->getArticlesUrl(),
            ])
            ->addCrumb('article',[
                'label' => $article->getTitle()
            ]);
        }
        $headBlock = $this->_view->getLayout()->getBlock('head');
        if ($headBlock) {
            if ($article->getMetaTitle()){
                $headBlock->setTitle($article->getMetaTitle());
            }
            else{
                $headBlock->setTitle($article->getTitle());
            }
            $headBlock->setKeywords($article->getMetaKeywords());
            $headBlock->setDescription($article->getMetaDescription());
        }
        $titleBlock = $this->_view->getLayout()->getBlock('page.main.title');
        if ($titleBlock) {
            $titleBlock->setPageTitle($article->getTitle());
        }
        $this->_view->renderLayout();
    }
}
