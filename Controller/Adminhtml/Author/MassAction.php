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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Sample\News\Api\AuthorRepositoryInterface;
use Sample\News\Controller\Adminhtml\Author;
use Sample\News\Model\Author as AuthorModel;
use Sample\News\Model\ResourceModel\Author\CollectionFactory;

abstract class MassAction extends Author
{
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var string
     */
    protected $successMessage;
    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @param Registry $registry
     * @param AuthorRepositoryInterface $authorRepository
     * @param PageFactory $resultPageFactory
     * @param Date $dateFilter
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param $successMessage
     * @param $errorMessage
     */
    public function __construct(
        Registry $registry,
        AuthorRepositoryInterface $authorRepository,
        PageFactory $resultPageFactory,
        Date $dateFilter,
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        $successMessage,
        $errorMessage
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->successMessage    = $successMessage;
        $this->errorMessage      = $errorMessage;
        parent::__construct($registry, $authorRepository, $resultPageFactory, $dateFilter, $context);
    }

    /**
     * @param AuthorModel $author
     * @return mixed
     */
    protected abstract function massAction(AuthorModel $author);

    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            foreach ($collection as $author) {
                $this->massAction($author);
            }
            $this->messageManager->addSuccessMessage(__($this->successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __($this->errorMessage));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('sample_news/*/index');
        return $redirectResult;
    }
}
