<?php
namespace Sample\News\Controller\Adminhtml\Author;

use \Sample\News\Controller\Adminhtml\Author;
use \Sample\News\Model\AuthorFactory;
use \Magento\Backend\App\Action\Context;
use \Magento\Backend\Model\View\Result\RedirectFactory;

class Delete extends Author
{
    /**
     * author factory
     *
     * @var \Sample\News\Model\AuthorFactory
     */
    protected $authorFactory;

    /**
     * @param AuthorFactory $authorFactory
     * @param Context $context
     * @param RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        AuthorFactory $authorFactory,
        Context $context,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->authorFactory = $authorFactory;
        parent::__construct($context, $resultRedirectFactory);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('author_id');
        if ($id) {
            $name = "";
            try {
                $author = $this->authorFactory->create();
                $author->load($id);
                $name = $author->getName();
                $author->delete();
                $this->messageManager->addSuccess(__('The author has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_sample_news_author_on_delete',
                    array('name' => $name, 'status' => 'success')
                );
                $resultRedirect->setPath('sample_news/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_sample_news_author_on_delete',
                    array('name' => $name, 'status' => 'fail')
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('sample_news/*/edit', array('author_id' => $id));
                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a author to delete.'));
        // go to grid
        $resultRedirect->setPath('sample_news/*/');
        return $resultRedirect;
    }
}
