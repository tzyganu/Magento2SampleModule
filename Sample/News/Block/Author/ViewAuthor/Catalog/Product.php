<?php
namespace Sample\News\Block\Author\ViewAuthor\Catalog;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Core\Helper\PostData;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product\Visibility;

class Product extends ListProduct
{
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $coreRegistry = null;
    /**
     * @var \Magento\Catalog\Model\Product\Visibility|null
     */
    protected $productVisibility = null;

    /**
     * @param Visibility $productVisibility
     * @param Registry $coreRegistry
     * @param Context $context
     * @param PostData $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param array $data
     */
    public function __construct(
        Visibility $productVisibility,
        Registry $coreRegistry,
        Context $context,
        PostData $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        $this->productVisibility = $productVisibility;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository);
    }

    /**
     * @access public
     * @return \Sample\News\Model\Author
     */
    public function getAuthor(){
        return $this->coreRegistry->registry('current_author');
    }

    /**
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _getProductCollection() {
        //todo: cache collection in member var
        $collection = $this->getAuthor()->getSelectedProductsCollection()
            ->setStore($this->_storeManager->getStore())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->setVisibility($this->productVisibility->getVisibleInCatalogIds());
        $collection->getSelect()->order('position');
        return $collection;
    }
}
