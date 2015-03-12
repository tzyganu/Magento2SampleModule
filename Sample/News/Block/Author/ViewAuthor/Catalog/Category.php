<?php
namespace Sample\News\Block\Author\ViewAuthor\Catalog;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\BlockFactory;

/**
 * @method Product setTitle(\string $title)
 */
class Category extends Template {
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $coreRegistry;
    /**
     * @var \Magento\Catalog\Model\Resource\Category\Collection
     */
    protected $categoryCollection;

    protected $blockFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        BlockFactory $blockFactory,
        Context $context,
        array $data = array()
    )
    {
        $this->coreRegistry = $registry;
        $this->blockFactory = $blockFactory;
        parent::__construct($context, $data);
        $this->setTabTitle();
    }

    /**
     * @return \Sample\News\Model\Author
     */
    public function getAuthor()
    {
        return $this->coreRegistry->registry('current_author');
    }

    /**
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function getCategoryCollection()
    {
        if (is_null($this->categoryCollection)) {
            $this->categoryCollection = $this->getAuthor()->getSelectedCategoriesCollection()
                ->setStore($this->_storeManager->getStore())
                ->addAttributeToSelect(array('name', 'url_key', 'url_path'))
                ->addAttributeToFilter('is_active', 1);
            $this->categoryCollection->getSelect()->order('at_position.position');

        }
        return $this->categoryCollection;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        /** @var \Magento\Theme\Block\Html\Pager $pager */
        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager');
        $pager->setNameInLayout('sample_news.author.view.category.pager');
        $pager->setPageVarName('p-category');
        $pager->setFragment('sample_news.author.view.category');
        $pager->setLimitVarName('l-category');
        $pager->setCollection($this->getCategoryCollection());
        $this->setChild('sample_news.author.view.category.pager', $pager);
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('sample_news.author.view.category.pager');
    }

    /**
     * @return $this
     */
    public function setTabTitle()
    {
        $title = $this->getCollectionSize()
            ? __('Categories %1', '<span class="counter">' . $this->getCollectionSize() . '</span>')
            : __('Categories');
        $this->setTitle($title);
        return $this;
    }

    /**
     * @return int
     */
    public function getCollectionSize()
    {
        return $this->getCategoryCollection()->getSize();
    }
}
