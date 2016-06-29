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

use Sample\News\Controller\Adminhtml\Author;
use Sample\News\Controller\RegistryConstants;

class Edit extends Author
{
    /**
     * Initialize current author and set it in the registry.
     *
     * @return int
     */
    protected function _initAuthor()
    {
        $authorId = $this->getRequest()->getParam('author_id');
        $this->coreRegistry->register(RegistryConstants::CURRENT_AUTHOR_ID, $authorId);

        return $authorId;
    }

    /**
     * Edit or create author
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $authorId = $this->_initAuthor();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Sample_News::author');
        $resultPage->getConfig()->getTitle()->prepend(__('Authors'));
        $resultPage->addBreadcrumb(__('News'), __('News'));
        $resultPage->addBreadcrumb(__('Authors'), __('Authors'), $this->getUrl('sample_news/author'));

        if ($authorId === null) {
            $resultPage->addBreadcrumb(__('New Author'), __('New Author'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Author'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Author'), __('Edit Author'));
            $resultPage->getConfig()->getTitle()->prepend(
                $this->authorRepository->getById($authorId)->getName()
            );
        }
        return $resultPage;
    }
}
