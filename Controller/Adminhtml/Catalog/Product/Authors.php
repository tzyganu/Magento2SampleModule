<?php
namespace Sample\News\Controller\Adminhtml\Catalog\Product;

use Magento\Catalog\Controller\Adminhtml\Product\Edit;

class Authors extends Edit
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
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    )
    {
        parent::__construct($context, $productBuilder, $resultPageFactory, $resultRedirectFactory);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * @return $this|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $productId = (int) $this->getRequest()->getParam('id');
        $product = $this->productBuilder->build($this->getRequest());

        if ($productId && !$product->getId()) {
            $this->messageManager->addError(__('This product no longer exists.'));
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('catalog/*/');
        }
        $resultLayout = $this->resultLayoutFactory->create();
        /** @var \Sample\News\Block\Adminhtml\Catalog\Product\Edit\Tab\Author $authorsBlock */
        $authorsBlock = $resultLayout->getLayout()->getBlock('sample_news.author');
        if ($authorsBlock) {
            $authorsBlock->setProductAuthors($this->getRequest()->getPost('product_authors', null));
        }
        return $resultLayout;
    }
}
