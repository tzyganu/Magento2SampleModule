<?php
namespace Sample\News\Controller\Adminhtml;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Backend\Model\View\Result\RedirectFactory;

class Author extends Action
{
    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @param Context $context
     * @param RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        Context $context,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }
}
