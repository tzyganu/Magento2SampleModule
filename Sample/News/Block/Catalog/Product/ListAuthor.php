<?php
namespace Sample\News\Block\Catalog\Product;

use Sample\News\Model\Author;
use Sample\News\Model\Author\Product as AuthorProduct;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\BlockFactory;

/**
 * @method ListAuthor setTitle(\string $title)
 */
class ListAuthor extends Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Sample\News\Model\Author\Product
     */
    protected $authorProduct;

    protected $blockFactory;

    /**
     * @var \Sample\News\Model\Resource\Author\Collection|null
     */
    protected $authorCollection;

    /**
     * @param AuthorProduct $authorProduct
     * @param Registry $registry
     * @param BlockFactory $blockFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        AuthorProduct $authorProduct,
        Registry $registry,
        BlockFactory $blockFactory,
        Context $context,
        array $data = []
    )
    {
        $this->authorProduct = $authorProduct;
        $this->registry = $registry;
        $this->blockFactory = $blockFactory;
        parent::__construct($context, $data);
        $this->setTabTitle();
    }

    /**
     * @return \Sample\News\Model\Resource\Author\Collection
     */
    public function getAuthorCollection()
    {
        if (is_null($this->authorCollection)) {
            $collection = $this->authorProduct->getSelectedAuthorsCollection($this->getProduct());
            $collection->addStoreFilter($this->_storeManager->getStore()->getId());
            $collection->addFieldToFilter('is_active', Author::STATUS_ENABLED);
            $collection->getSelect()->order('position');
            $this->authorCollection = $collection;
        }
        return $this->authorCollection;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        /** @var \Magento\Theme\Block\Html\Pager $pager */
        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager');
        $pager->setNameInLayout('sample_news.author.list.pager');
        $pager->setPageVarName('p-author');
        $pager->setLimitVarName('l-author');
        $pager->setFragment('catalog.product.list.sample.news.author');
        $pager->setCollection($this->getAuthorCollection());
        $this->setChild('pager', $pager);
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return $this
     */
    public function setTabTitle()
    {
        $title = $this->getCollectionSize()
            ? __('Authors %1', '<span class="counter">' . $this->getCollectionSize() . '</span>')
            : __('Authors');
        $this->setTitle($title);
        return $this;
    }

    /**
     * @return int
     */
    public function getCollectionSize()
    {
        return $this->getAuthorCollection()->getSize();
    }
}
