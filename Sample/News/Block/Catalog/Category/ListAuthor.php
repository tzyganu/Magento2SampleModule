<?php
namespace Sample\News\Block\Catalog\Category;

use Magento\Framework\View\Element\Template;
use Sample\News\Model\Author\Category as CategoryModel;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

class ListAuthor extends Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Sample\News\Model\Author\Category
     */
    protected $categoryModel;

    /**
     * @var \Sample\News\Model\Resource\Author\Collection
     */
    protected $authorCollection;

    /**
     * @param CategoryModel $categoryModel
     * @param Registry $registry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        CategoryModel $categoryModel,
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->categoryModel = $categoryModel;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return \Sample\News\Model\Resource\Author\Collection
     */
    public function getAuthorCollection()
    {
        if (is_null($this->authorCollection)) {
            $this->authorCollection = $this->categoryModel
                ->getSelectedAuthorsCollection($this->getCategory())
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addFieldToFilter('is_active', 1);//TODO: use constant here
            $this->authorCollection->getSelect()->order('related_category.position');
        }
        return $this->authorCollection;
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        //TODO: use block factory here
        /** @var \Magento\Theme\Block\Html\Pager $pager */
        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager');
        $pager->setNameInLayout('sample_news.author.list.pager');
        $pager->setPageVarName('p-author');
        $pager->setLimitVarName('l-author');
        $pager->setCollection($this->getAuthorCollection());
        $this->setChild('pager', $pager);
        $this->getAuthorCollection()->load();
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
