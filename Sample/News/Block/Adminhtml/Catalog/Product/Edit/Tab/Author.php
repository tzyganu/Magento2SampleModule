<?php
namespace Sample\News\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Registry;
use Sample\News\Model\ResourceModel\Author\CollectionFactory as AuthorCollectionFactory;
use Sample\News\Model\Author\Product as AuthorProduct;
use Magento\Catalog\Controller\Adminhtml\Product\Builder as ProductBuilder;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;

/**
 * @method Author setUseAjax(\bool $useAjax)
 * @method array getProductAuthors()
 * @method Author setProductAuthors(array $authors)
 */
class Author extends ExtendedGrid implements TabInterface
{
    /**
     * @var \Sample\News\Model\ResourceModel\Author\CollectionFactory
     */
    protected $authorCollectionFactory;
    /**
     * @var \Sample\News\Model\Author\Product
     */
    protected $authorProduct;
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $registry;
    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product\Builder
     */
    protected $productBuilder;
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @param AuthorCollectionFactory $authorCollectionFactory
     * @param AuthorProduct $authorProduct
     * @param Registry $registry
     * @param ProductBuilder $productBuilder
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param array $data
     */
    public function __construct(
        AuthorCollectionFactory $authorCollectionFactory,
        AuthorProduct $authorProduct,
        Registry $registry,
        ProductBuilder $productBuilder,
        Context $context,
        BackendHelper $backendHelper,
        array $data = []
    )
    {
        $this->authorCollectionFactory = $authorCollectionFactory;
        $this->authorProduct = $authorProduct;
        $this->registry = $registry;
        $this->productBuilder = $productBuilder;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * set grid parameters
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('author_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getProduct()->getId()) {
            $this->setDefaultFilter(['in_authors'=>1]);
        }
    }

    /**
     * prepare collection
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->authorCollectionFactory->create();
        if ($this->getProduct()->getId()) {
            $constraint = 'related.product_id='.$this->getProduct()->getId();
        } else {
            $constraint = 'related.product_id=0';
        }
        $collection->getSelect()->joinLeft(
            ['related' => $collection->getTable('sample_news_author_product')],
            'related.author_id = main_table.author_id AND '.$constraint,
            ['position']
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * no mass action here
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
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
                'type'  => 'checkbox',
                'name'  => 'in_authors',
                'values'=> $this->_getSelectedAuthors(),
                'align' => 'center',
                'index' => 'author_id',
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select'
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
     * @return array
     */
    protected function _getSelectedAuthors()
    {
        $authors = $this->getProductAuthors();
        if (!is_array($authors)) {
            $authors = array_keys($this->getSelectedAuthors());
        }
        return $authors;
    }

    /**
     * get selected authors
     * @return array
     */
    public function getSelectedAuthors()
    {
        $authors = [];
        $selected = $this->authorProduct->getSelectedAuthors($this->getProduct());
        if (!is_array($selected)) {
            $selected = [];
        }
        foreach ($selected as $author) {
            /** @var \Sample\News\Model\Author $author */
            $authors[$author->getId()] = ['position' => $author->getPosition()];
        }
        return $authors;
    }

    /**
     * get row url
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $item
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
        return $this->_urlBuilder->getUrl(
            '*/*/authorsGrid',
            [
                'id'=>$this->getProduct()->getId()
            ]
        );
    }

    /**
     * get current product
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (is_null($this->_product)) {
            if ($this->registry->registry('current_product')) {
                $this->_product = $this->registry->registry('current_product');
            } else {
                $product = $this->productBuilder->build($this->getRequest());
                $this->_product = $product;
            }
        }
        return $this->_product;
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
                if ($authorIds) {
                    $this->getCollection()->addFieldToFilter('author_id', ['nin'=>$authorIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Authors');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('sample_news/catalog_product/authors', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
