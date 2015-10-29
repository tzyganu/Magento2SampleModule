<?php
namespace Sample\News\Block\Adminhtml\Author\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as DataHelper;
use Magento\Store\Model\Store;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as SetCollectionFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Directory\Model\Currency;
use Magento\Store\Model\ScopeInterface;

/**
 * @method Product setUseAjax(\bool $useAjax)
 * @method array|null getAuthorProducts()
 * @method Product setAuthorProducts(array $products)
 */
class Product extends ExtendedGrid implements TabInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $status;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $visibility;

    /**
     * @var  \Magento\Framework\Registry
     */
    protected $coreRegistry;
    protected $setCollectionFactory;
    protected $productFactory;

    /**
     * @param CollectionFactory $productCollectionFactory
     * @param ProductType $type
     * @param ProductStatus $status
     * @param ProductVisibility $visibility
     * @param Registry $coreRegistry
     * @param SetCollectionFactory $setsFactory
     * @param ProductFactory $productFactory
     * @param Context $context
     * @param DataHelper $backendHelper
     * @param array $data
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        ProductType $type,
        ProductStatus $status,
        ProductVisibility $visibility,
        Registry $coreRegistry,
        SetCollectionFactory $setsFactory,
        ProductFactory $productFactory,
        Context $context,
        DataHelper $backendHelper,
        array $data = []
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->type = $type;
        $this->status = $status;
        $this->visibility = $visibility;
        $this->coreRegistry = $coreRegistry;
        $this->setCollectionFactory = $setsFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('product_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getAuthor()->getId()) {
            $this->setDefaultFilter(['in_products'=>1]);
        }
    }

    /**
     * prepare the collection
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('price');
        $adminStore = Store::DEFAULT_STORE_ID;
        $collection->joinAttribute('product_name', 'catalog_product/name', 'entity_id', null, 'left', $adminStore);
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'left', $adminStore);
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'left', $adminStore);
        if ($this->getAuthor()->getId()) {
            $constraint = '{{table}}.author_id='.$this->getAuthor()->getId();
        } else {
            $constraint = '{{table}}.author_id=0';
        }
        $collection->joinField(
            'position',
            'sample_news_author_product',
            'position',
            'product_id = entity_id',
            $constraint,
            'left'
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_products',
            [
                'header_css_class'  => 'a-center',
                'type'   => 'checkbox',
                'name'   => 'in_products',
                'values' => $this->_getSelectedProducts(),
                'align'  => 'center',
                'index'  => 'entity_id'
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id',
                'type' => 'number',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'product_name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'type',
            [
                'header' => __('Type'),
                'index' => 'type_id',
                'type' => 'options',
                'options' => $this->type->getOptionArray(),
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type'
            ]
        );
        /** @var \Magento\Catalog\Model\ResourceModel\Product $resource */
        $resource = $this->productFactory->create()->getResource();
        $sets = $this->setCollectionFactory->create()->setEntityTypeFilter(
            $resource->getTypeId()
        )->load()->toOptionHash();

        $this->addColumn(
            'set_name',
            [
                'header' => __('Attribute Set'),
                'index' => 'attribute_set_id',
                'type' => 'options',
                'options' => $sets,
                'header_css_class' => 'col-attr-name',
                'column_css_class' => 'col-attr-name'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->status->getOptionArray(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );

        $this->addColumn(
            'visibility',
            [
                'header' => __('Visibility'),
                'index' => 'visibility',
                'type' => 'options',
                'options' => $this->visibility->getOptionArray(),
                'header_css_class' => 'col-visibility',
                'column_css_class' => 'col-visibility'
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku'
            ]
        );

        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'currency_code' => (string)$this->_scopeConfig->getValue(
                    Currency::XML_PATH_CURRENCY_BASE,
                    ScopeInterface::SCOPE_STORE
                ),
                'index' => 'price',
                'header_css_class' => 'col-price',
                'column_css_class' => 'col-price'
            ]
        );
        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'name'   => 'position',
                'width'  => 60,
                'type'   => 'number',
                'validate_class' => 'validate-number',
                'index' => 'position',
                'editable'  => true,
            ]
        );
        return $this;
    }

    /**
     * Retrieve selected products
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getAuthorProducts();
        if (!is_array($products)) {
            $products = $this->getAuthor()->getProductsPosition();
            return array_keys($products);
        }
        return $products;
    }
    /**
     * Retrieve selected products
     * @return array
     */
    public function getSelectedProducts()
    {
        $selected = $this->getAuthor()->getProductsPosition();
        if (!is_array($selected)) {
            $selected = [];
        }
        return $selected;
    }
    /**
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
        return $this->getUrl(
            '*/*/productsGrid',
            [
                'author_id' => $this->getAuthor()->getId()
            ]
        );
    }

    /**
     * @access public
     * @return \Sample\News\Model\Author
     */
    public function getAuthor()
    {
        return $this->coreRegistry->registry('sample_news_author');
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in'=>$productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin'=>$productIds]);
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
        return __('Products');
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
        return $this->getUrl('sample_news/author/products', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
