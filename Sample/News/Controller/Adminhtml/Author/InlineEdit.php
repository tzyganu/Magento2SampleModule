<?php
namespace Sample\News\Controller\Adminhtml\Author;

use Magento\Backend\App\Action\Context;
use Sample\News\Model\AuthorFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Sample\News\Controller\Adminhtml\Author as AuthorController;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\Registry;
use Sample\News\Model\Author;
use Magento\Framework\Stdlib\DateTime\Filter\Date;


class InlineEdit extends AuthorController
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param JsonFactory $jsonFactory
     * @param Registry $registry
     * @param AuthorFactory $authorFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param Date $dateFilter
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        Registry $registry,
        AuthorFactory $authorFactory,
        RedirectFactory $resultRedirectFactory,
        Date $dateFilter,
        Context $context

    ) {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($registry, $authorFactory, $resultRedirectFactory, $dateFilter, $context);

    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $authorId) {
            /** @var \Sample\News\Model\Author $author */
            $author = $this->authorFactory->create()->load($authorId);
            try {
                $authorData = $this->filterData($postItems[$authorId]);
                $author->addData($authorData);

                $author->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithAuthorId($author, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithAuthorId($author, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithAuthorId(
                    $author,
                    __('Something went wrong while saving the page.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add author id to error message
     *
     * @param Author $author
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithAuthorId(Author $author, $errorText)
    {
        return '[Author ID: ' . $author->getId() . '] ' . $errorText;
    }
}
