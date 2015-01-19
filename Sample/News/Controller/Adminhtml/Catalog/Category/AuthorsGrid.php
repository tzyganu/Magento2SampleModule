<?php
namespace Sample\News\Controller\Adminhtml\Catalog\Category;

use Magento\Catalog\Controller\Adminhtml\Category;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\View\Result\LayoutFactory;

class AuthorsGrid extends Category
{

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Context $context,
        RedirectFactory $resultRedirectFactory,
        LayoutFactory $resultLayoutFactory
    )
    {
        parent::__construct($context, $resultRedirectFactory);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute(){

        $this->_initCategory();
        $resultLayout = $this->resultLayoutFactory->create();
        /** @var \Sample\News\Block\Adminhtml\Catalog\Category\Tab\Author $authorsBlock */
        $authorsBlock = $resultLayout->getLayout()->getBlock('category.sample_news.author.grid');
        if ($authorsBlock) {
            $authorsBlock->setCategoryAuthors($this->getRequest()->getPost('category_sample_news_authors', null));
        }
        return $resultLayout;
    }
}
