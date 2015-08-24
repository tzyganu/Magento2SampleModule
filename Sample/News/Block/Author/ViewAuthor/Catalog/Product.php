<?php
namespace Sample\News\Block\Author\ViewAuthor\Catalog;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Url\Helper\Data as UrlHelper;

/**
 * @method Product setTitle(\string $title)
 */
class Product extends ListProduct
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Collection
     */
    protected $productCollection;

    /**
     * @param Visibility $productVisibility
     * @param Registry $coreRegistry
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param UrlHelper $urlHelper
     * @param array $data
     */
    public function __construct(
        Visibility $productVisibility,
        Registry $coreRegistry,
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        UrlHelper $urlHelper,
        array $data = []
    )
    {
        $this->productVisibility = $productVisibility;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
        $this->setTabTitle();
    }

    /**
     * @access public
     * @return \Sample\News\Model\Author
     */
    public function getAuthor()
    {
        return $this->coreRegistry->registry('current_author');
    }

    /**
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->productCollection)) {
            $collection = $this->getAuthor()->getSelectedProductsCollection()
                ->setStore($this->_storeManager->getStore())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addStoreFilter()
                ->addUrlRewrite()
                ->setVisibility($this->productVisibility->getVisibleInCatalogIds());
            $collection->getSelect()->order('position');
            $this->productCollection = $collection;
        }
        return $this->productCollection;
    }

    /**
     * @return $this
     */
    public function setTabTitle()
    {
        $title = $this->getCollectionSize()
            ? __('Products %1', '<span class="counter">' . $this->getCollectionSize() . '</span>')
            : __('Products');
        $this->setTitle($title);
        return $this;
    }

    /**
     * @return int
     */
    public function getCollectionSize()
    {
        return $this->_getProductCollection()->getSize();
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        /** @var \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar */
        $toolbar = $this->getChildBlock('toolbar');
        /** @var \Magento\Theme\Block\Html\Pager $pager */
        $pager = $toolbar->getChildBlock('product_list_toolbar_pager');
        $pager->setFragment('sample_news.author.view.product');
        return $this;
    }
}
