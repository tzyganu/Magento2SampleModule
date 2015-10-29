<?php
namespace Sample\News\Model\Author;

use Sample\News\Model\ResourceModel\Author\CollectionFactory;
use Magento\Catalog\Model\Product as ProductModel;

class Product
{
    /**
     * @var \Sample\News\Model\AuthorFactory
     */
    protected $authorCollectionFactory;

    /**
     * @param CollectionFactory $authorCollectionFactory
     */
    public function __construct(CollectionFactory $authorCollectionFactory)
    {
        $this->authorCollectionFactory = $authorCollectionFactory;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return \Sample\News\Model\Author[]
     */
    public function getSelectedAuthors(ProductModel $product)
    {
        if (!$product->hasSelectedAuthors()) {
            $authors = [];
            foreach ($this->getSelectedAuthorsCollection($product) as $author) {
                $authors[] = $author;
            }
            $product->setSelectedAuthors($authors);
        }
        return $product->getData('selected_authors');
    }

    /**
     * @access public
     * @param \Magento\Catalog\Model\Product $product
     * @return \Sample\News\Model\ResourceModel\Author\Collection
     */
    public function getSelectedAuthorsCollection(ProductModel $product)
    {
        $collection = $this->authorCollectionFactory->create()
            ->addProductFilter($product);
        return $collection;
    }
}
