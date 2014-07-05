<?php
namespace Sample\News\Controller\Adminhtml\Catalog;

class Product extends \Magento\Catalog\Controller\Adminhtml\Product {
    public function articlesAction(){
        $productId = (int) $this->getRequest()->getParam('id');
        $product = $this->productBuilder->build($this->getRequest());
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('product.edit.tab.article')
            ->setProductArticles($this->getRequest()->getPost('product_articles', null));
        $this->_view->renderLayout();
    }
    public function articlesGridAction(){
        $productId = (int) $this->getRequest()->getParam('id');
        $product = $this->productBuilder->build($this->getRequest());
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('product.edit.tab.article')
           ->setProductArticles($this->getRequest()->getPost('product_articles', null));
        $this->_view->renderLayout();
    }
}