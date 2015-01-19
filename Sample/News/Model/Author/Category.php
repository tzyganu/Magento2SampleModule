<?php
namespace Sample\News\Model\Author;

use Sample\News\Model\Resource\Author\CollectionFactory;
use Magento\Catalog\Model\Category as CategoryModel;

class Category
{
    /**
     * @var null|\Sample\News\Model\AuthorFactory
     */
    protected $authorCollectionFactory;

    /**
     * @param CollectionFactory $authorCollectionFactory
     */
    public function __construct(
        CollectionFactory $authorCollectionFactory
    )
    {
        $this->authorCollectionFactory = $authorCollectionFactory;
    }

    /**
     * @access public
     * @param \Magento\Catalog\Model\Category $category
     * @return mixed
     */
    public function getSelectedAuthors(CategoryModel $category)
    {
        if (!$category->hasSelectedAuthors()) {
            $authors = [];
            foreach ($this->getSelectedAuthorsCollection($category) as $author) {
                $authors[] = $author;
            }
            $category->setSelectedAuthors($authors);
        }
        return $category->getData('selected_authors');
    }

    /**
     * @access public
     * @param \Magento\Catalog\Model\Category $category
     * @return mixed
     */
    public function getSelectedAuthorsCollection(CategoryModel $category)
    {
        $collection = $this->authorCollectionFactory->create()
            ->addCategoryFilter($category);
        return $collection;
    }
}
