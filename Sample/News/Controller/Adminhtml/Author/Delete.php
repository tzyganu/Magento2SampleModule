<?php
namespace Sample\News\Controller\Adminhtml\Author;

use \Sample\News\Controller\Adminhtml\Author;

class Delete extends Author
{
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
                /** @var \Sample\News\Model\Author $author */
                $author = $this->authorFactory->create();
                $author->load($id);
                $name = $author->getName();
                $author->delete();
                $this->messageManager->addSuccess(__('The author has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_sample_news_author_on_delete',
                    ['name' => $name, 'status' => 'success']
                );
                $resultRedirect->setPath('sample_news/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_sample_news_author_on_delete',
                    ['name' => $name, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('sample_news/*/edit', ['author_id' => $id]);
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
