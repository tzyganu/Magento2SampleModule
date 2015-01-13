<?php
namespace Sample\News\Controller\Adminhtml;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Backend\Model\View\Result\RedirectFactory;
use \Sample\News\Model\AuthorFactory;
use \Magento\Framework\Registry;

class Author extends Action
{
    /**
     * author factory
     *
     * @var AuthorFactory
     */
    protected $authorFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @param Registry $registry
     * @param AuthorFactory $authorFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        AuthorFactory $authorFactory,
        RedirectFactory $resultRedirectFactory,
        Context $context

    ) {
        $this->coreRegistry = $registry;
        $this->authorFactory = $authorFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    /**
     * @return \Sample\News\Model\Author
     */
    protected function initAuthor() {
        $authorId  = (int) $this->getRequest()->getParam('author_id');
        /** @var \Sample\News\Model\Author $author */
        $author    = $this->authorFactory->create();
        if ($authorId) {
            $author->load($authorId);
        }
        $this->coreRegistry->register('sample_news_author', $author);
        return $author;
    }

}
