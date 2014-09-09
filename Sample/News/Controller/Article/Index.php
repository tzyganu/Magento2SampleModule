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
class Index
    extends \Sample\News\Controller\Article {
    /**
     * article list
     */
    public function execute() {
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
}
