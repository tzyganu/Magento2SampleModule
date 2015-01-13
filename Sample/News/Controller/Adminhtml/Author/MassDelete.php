<?php
namespace Sample\News\Controller\Adminhtml\Author;

use \Sample\News\Controller\Adminhtml\Author;
use \Sample\News\Model\AuthorFactory;
use \Magento\Backend\App\Action\Context;
use \Magento\Backend\Model\View\Result\RedirectFactory;
use \Magento\Framework\Model\Exception as FrameworkException;

class MassDelete extends Author
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $authorIds = $this->getRequest()->getParam('author_ids');

        if (!is_array($authorIds)) {
            $this->messageManager->addError(__('Please select authors.'));
        } else {
            try {
                foreach ($authorIds as $reviewId) {
                    /** @var \Sample\News\Model\Author $author */
                    $author = $this->authorFactory->create()->load($reviewId);
                    $author->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($authorIds))
                );
            } catch (FrameworkException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('An error occurred while deleting record(s).'));
            }
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('sample_news/*/index');
        return $redirectResult;
    }
}
