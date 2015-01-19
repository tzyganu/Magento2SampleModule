<?php
namespace Sample\News\Block\Adminhtml\Catalog\Category\Tab;

use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Sample\News\Model\Resource\Author\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use Sample\News\Model\Author\Category as AuthorCategory;

/**
 * @method Author setCategoryAuthors(array $authors)
 */
class Author extends ExtendedGrid
{
    /**
     * @var \Sample\News\Model\AuthorFactory
     */
    protected $authorCollectionFactory;

    /**
     * @var \Sample\News\Model\Author\Category
     */
    protected $authorCategory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param CollectionFactory $authorCollectionFactory
     * @param AuthorCategory $authorCategory
     * @param Registry $registry
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param array $data
     */
    public function __construct(
        CollectionFactory $authorCollectionFactory,
        AuthorCategory $authorCategory,
        Registry $registry,
        Context $context,
        BackendHelper $backendHelper,
        array $data = array()
    ) {
        $this->authorCollectionFactory = $authorCollectionFactory;
        $this->authorCategory = $authorCategory;
        $this->registry = $registry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @access public
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('catalog_category_sample_news_author');
        $this->setDefaultSort('author_id');
        $this->setUseAjax(true);
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(['in_authors'=>1]);
        }
    }

    /**
     * get current category
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * prepare collection
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->authorCollectionFactory->create();
        if ($this->getCategory()->getId()){
            $constraint = 'related.category_id='.$this->getCategory()->getId();
        }
        else{
            $constraint = 'related.category_id=0';
        }
        $collection->getSelect()->joinLeft(
            ['related' => $collection->getTable('sample_news_author_category')],
            'related.author_id=main_table.author_id AND '.$constraint,
            ['position']
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_authors',
            [
                'header_css_class'  => 'a-center',
                'type'  => 'checkbox',
                'name'  => 'in_authors',
                'values'=> $this->_getSelectedAuthors(),
                'align' => 'center',
                'index' => 'author_id'
            ]
        );
        $this->addColumn(
            'author_id',
            [
                'header'=> __('Id'),
                'type'  => 'number',
                'align' => 'left',
                'index' => 'author_id',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header'=> __('Name'),
                'align' => 'left',
                'index' => 'name',
            ]
        );
        $this->addColumn(
            'position',
            [
                'header'        => __('Position'),
                'name'          => 'position',
                'width'         => 60,
                'type'        => 'number',
                'validate_class'=> 'validate-number',
                'index'         => 'position',
                'editable'      => true,
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * get selected authors
     * @return array
     */
    protected function _getSelectedAuthors()
    {
        $authors = $this->getCategoryAuthors();
        if (!is_array($authors)) {
            $authors = array_keys($this->getSelectedAuthors());
        }
        return $authors;
    }

    /**
     * @access public
     * @return array
     */
    public function getSelectedAuthors()
    {
        $authors = array();
        $selected = $this->authorCategory->getSelectedAuthors($this->getCategory());
        if (!is_array($selected)){
            $selected = array();
        }
        foreach ($selected as $author) {
            /** @var \Sample\News\Model\Author $author */
            $authors[$author->getId()] = $author->getPosition();
        }
        return $authors;
    }

    /**
     * get row URL
     * @param \Sample\News\Model\Author $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'sample_news/catalog_category/authorsGrid',
            ['id'=>$this->getCategory()->getId()]
        );
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_authors') {
            $authorIds = $this->_getSelectedAuthors();
            if (empty($authorIds)) {
                $authorIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('author_id', ['in'=>$authorIds]);
            } else {
                if($authorIds) {
                    $this->getCollection()->addFieldToFilter('author_id', ['nin'=>$authorIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
