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
namespace Sample\News\Controller\Adminhtml\Article;

class Products
    extends \Sample\News\Controller\Adminhtml\Article {
    /**
     * products listing
     */
    public function execute() {
        $this->_initArticle();
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('article.edit.tab.product')
            ->setArticleProducts($this->getRequest()->getPost('article_products', null));
        $this->_view->renderLayout();
    }
}
