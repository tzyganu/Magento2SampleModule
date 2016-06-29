<?php
/**
 * Sample_News extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Sample
 * @package   Sample_News
 * @copyright 2016 Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru
 */
namespace Sample\News\Controller\Adminhtml\Author;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Sample\News\Controller\Adminhtml\Author;

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
            try {
                $this->authorRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The author has been deleted.'));
                $resultRedirect->setPath('sample_news/*/');
                return $resultRedirect;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The author no longer exists.'));
                return $resultRedirect->setPath('sample_news/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('sample_news/author/edit', ['author_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the author'));
                return $resultRedirect->setPath('sample_news/author/edit', ['author_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a author to delete.'));
        $resultRedirect->setPath('sample_news/*/');
        return $resultRedirect;
    }
}
