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
namespace Sample\News\Controller\Article;
class View
    extends \Sample\News\Controller\Article {
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
    public function execute() {
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
