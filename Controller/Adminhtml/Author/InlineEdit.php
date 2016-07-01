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

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Result\PageFactory;
use Sample\News\Api\AuthorRepositoryInterface;
use Sample\News\Api\Data\AuthorInterface;
use Sample\News\Api\Data\AuthorInterfaceFactory;
use Sample\News\Controller\Adminhtml\Author as AuthorController;
use Sample\News\Model\Author;
use Sample\News\Model\ResourceModel\Author as AuthorResourceModel;

class InlineEdit extends AuthorController
{
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var AuthorResourceModel
     */
    protected $authorResourceModel;

    /**
     * @param Registry $registry
     * @param AuthorRepositoryInterface $authorRepository
     * @param PageFactory $resultPageFactory
     * @param Date $dateFilter
     * @param Context $context
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param JsonFactory $jsonFactory
     * @param AuthorResourceModel $authorResourceModel
     */
    public function __construct(
        Registry $registry,
        AuthorRepositoryInterface $authorRepository,
        PageFactory $resultPageFactory,
        Date $dateFilter,
        Context $context,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        JsonFactory $jsonFactory,
        AuthorResourceModel $authorResourceModel
    )
    {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper    = $dataObjectHelper;
        $this->jsonFactory         = $jsonFactory;
        $this->authorResourceModel = $authorResourceModel;
        parent::__construct($registry, $authorRepository, $resultPageFactory, $dateFilter, $context);
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
            /** @var \Sample\News\Model\Author|AuthorInterface $author */
            $author = $this->authorRepository->getById((int)$authorId);
            try {
                $authorData = $this->filterData($postItems[$authorId]);
                $this->dataObjectHelper->populateWithArray($author, $authorData , AuthorInterface::class);
                $this->authorResourceModel->saveAttribute($author, array_keys($authorData));
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithAuthorId($author, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithAuthorId($author, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithAuthorId(
                    $author,
                    __('Something went wrong while saving the author.')
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
