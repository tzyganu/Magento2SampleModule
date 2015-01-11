<?php
namespace Sample\News\Controller\Author;

use \Magento\Framework\App\Action\Action;
use \Magento\Framework\App\Action\Context;
use \Sample\News\Model\AuthorFactory;
use \Magento\Framework\Controller\Result\ForwardFactory;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Framework\Registry;

class View extends Action
{
    protected $authorFactory;
    protected $resultForwardFactory;
    protected $resultPageFactory;
    protected $coreRegistry;
    public function __construct(
        Context $context,
        AuthorFactory $authorFactory,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        Registry $coreRegistry
    )
    {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->authorFactory = $authorFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }
    public function execute()
    {
        $authorId = (int) $this->getRequest()->getParam('id');
        $author = $this->authorFactory->create();
        $author->load($authorId);

        if (!$author->isActive()) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }

        $this->coreRegistry->register('current_author', $author);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($author->getMetaTitle());
        $resultPage->getConfig()->setDescription($author->getMetaDescription());
        $resultPage->getConfig()->setKeywords($author->getMetaKeywords());

        return $resultPage;
    }
}
