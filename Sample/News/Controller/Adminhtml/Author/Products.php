<?php
namespace Sample\News\Controller\Adminhtml\Author;

use Sample\News\Controller\Adminhtml\Author;
use Magento\Framework\Registry;
use Sample\News\Model\AuthorFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

class Products extends Author
{

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @param LayoutFactory $resultLayoutFactory
     * @param Registry $registry
     * @param AuthorFactory $authorFactory
     * @param Context $context
     * @param RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        LayoutFactory $resultLayoutFactory,
        Registry $registry,
        AuthorFactory $authorFactory,
        RedirectFactory $resultRedirectFactory,
        Date $dateFilter,
        Context $context

    )
    {
        $this->resultLayoutFactory = $resultLayoutFactory;
        parent::__construct($registry, $authorFactory, $resultRedirectFactory, $dateFilter, $context);
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->initAuthor();
        $resultLayout = $this->resultLayoutFactory->create();
        /** @var \Sample\News\Block\Adminhtml\Author\Edit\Tab\Product $productsBlock */
        $productsBlock = $resultLayout->getLayout()->getBlock('author.edit.tab.product');
        if ($productsBlock) {
            $productsBlock->setAuthorProducts($this->getRequest()->getPost('author_products', null));
        }
        return $resultLayout;
    }
}
