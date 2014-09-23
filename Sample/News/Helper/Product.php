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
namespace Sample\News\Helper;
class Product
    extends \Magento\Framework\App\Helper\AbstractHelper {
    /**
     * @var null|\Sample\News\Model\ArticleFactory
     */
    protected $_articleFactory = null;
    /**
     * @var null|\Sample\News\Model\SectionFactory
     */
    protected $_sectionFactory = null;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Sample\News\Model\ArticleFactory $articleFactory
     * @param \Sample\News\Model\SectionFactory $sectionFactory
     */
    public function __construct(
        \Sample\News\Model\ArticleFactory $articleFactory,
        \Sample\News\Model\SectionFactory $sectionFactory,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->_articleFactory = $articleFactory;
        $this->_sectionFactory = $sectionFactory;
        parent::__construct($context);
    }

    /**
     * @access public
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function getSelectedArticles(\Magento\Catalog\Model\Product $product){
        if (!$product->hasSelectedArticles()) {
            $articles = [];
            foreach ($this->getSelectedArticlesCollection($product) as $article) {
                $articles[] = $article;
            }
            $product->setSelectedArticles($articles);
        }
        return $product->getData('selected_articles');
    }

    /**
     * @access public
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function getSelectedArticlesCollection(\Magento\Catalog\Model\Product $product){
        $collection = $this->_articleFactory->create()->getResourceCollection()
            ->addProductFilter($product);
        return $collection;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function getSelectedSections(\Magento\Catalog\Model\Product $product){
        if (!$product->hasSelectedSections()) {
            $sections = [];
            foreach ($this->getSelectedSectionsCollection($product) as $section) {
                $sections[] = $section;
            }
            $product->setSelectedSections($sections);
        }
        return $product->getData('selected_sections');
    }

    /**
     * @access public
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function getSelectedSectionsCollection(\Magento\Catalog\Model\Product $product){
        $collection = $this->_sectionFactory->create()->getResourceCollection()
            ->addProductFilter($product);
        return $collection;
    }
}
