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
namespace Sample\News\Controller\Adminhtml\Catalog\Product;

class ArticlesGrid
    extends \Magento\Catalog\Controller\Adminhtml\Product {
    /**
     * product article grid
     */
    public function execute() {
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('product.edit.tab.article')
            ->setProductArticles($this->getRequest()->getPost('product_articles', null));
        $this->_view->renderLayout();
    }
}
