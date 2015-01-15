<?php
namespace Sample\News\Controller\Adminhtml\Author;

use Sample\News\Controller\Adminhtml\Author as AuthorController;
use Magento\Framework\Registry;
use Sample\News\Model\AuthorFactory;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;

class Edit extends AuthorController
{
    /**
     * backend session
     *
     * @var BackendSession
     */
    protected $backendSession;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * constructor
     *
     * @param Registry $registry
     * @param AuthorFactory $authorFactory
     * @param BackendSession $backendSession
     * @param PageFactory $resultPageFactory
     * @param Context $context
     * @param RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        Registry $registry,
        AuthorFactory $authorFactory,
        BackendSession $backendSession,
        PageFactory $resultPageFactory,
        Context $context,
        RedirectFactory $resultRedirectFactory
    )
    {
        $this->backendSession = $backendSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($registry, $authorFactory, $resultRedirectFactory, $context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sample_News::author');
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('author_id');
        /** @var \Sample\News\Model\Author $author */
        $author = $this->initAuthor();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Sample_News::author');
        $resultPage->getConfig()->getTitle()->set((__('Authors')));
        if ($id) {
            $author->load($id);
            if (!$author->getId()) {
                $this->messageManager->addError(__('This author no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath(
                    'sample_news/*/edit',
                    [
                        'author_id' => $author->getId(),
                        '_current' => true
                    ]
                );
                return $resultRedirect;
            }
        }
        $title = $author->getId() ? $author->getName() : __('New Author');
        $resultPage->getConfig()->getTitle()->append($title);
        $data = $this->backendSession->getData('sample_news_author_data', true);
        if (!empty($data)) {
            $author->setData($data);
        }
        return $resultPage;
    }
}
