<?php
namespace Sample\News\Controller\Adminhtml\Author;

use Magento\Framework\Exception\LocalizedException;
use Sample\News\Controller\Adminhtml\Author;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Sample\News\Model\AuthorFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Ui\Component\MassAction\Filter;
use Sample\News\Model\ResourceModel\Author\CollectionFactory;
use Sample\News\Model\Author as AuthorModel;

abstract class MassAction extends Author
{
    protected $filter;
    protected $collectionFactory;
    /**
     * @var string
     */
    protected $successMessage = 'Mass Action successful on %1 records';
    /**
     * @var string
     */
    protected $errorMessage = 'Mass Action failed';

    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory,
        Registry $registry,
        AuthorFactory $authorFactory,
        RedirectFactory $resultRedirectFactory,
        Date $dateFilter,
        Context $context
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($registry, $authorFactory, $resultRedirectFactory, $dateFilter, $context);
    }

    /**
     * @param AuthorModel $author
     * @return mixed
     */
    protected abstract function doTheAction(AuthorModel $author);

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
                $this->doTheAction($author);
            }
            $this->messageManager->addSuccess(__($this->successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __($this->errorMessage));
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('sample_news/*/index');
        return $redirectResult;
    }
}
