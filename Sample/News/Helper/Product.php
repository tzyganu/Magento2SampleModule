<?php
namespace Sample\News\Helper;
class Product extends \Magento\Framework\App\Helper\AbstractHelper {
    protected $_articleFactory = null;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Sample\News\Model\ArticleFactory $articleFactory
    ) {
        $this->_articleFactory = $articleFactory;
        parent::__construct($context);
    }
    public function getSelectedArticles(\Magento\Catalog\Model\Product $product){
        if (!$product->hasSelectedArticles()) {
            $articles = array();
            foreach ($this->getSelectedArticlesCollection($product) as $article) {
                $articles[] = $article;
            }
            $product->setSelectedArticles($articles);
        }
        return $product->getData('selected_articles');
    }
    public function getSelectedArticlesCollection(\Magento\Catalog\Model\Product $product){
        $collection = $this->_articleFactory->create()->getCollection()
            ->addProductFilter($product);
        return $collection;
    }
}