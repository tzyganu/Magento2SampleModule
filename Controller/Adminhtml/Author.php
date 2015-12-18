<?php
namespace Sample\News\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Sample\News\Model\AuthorFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

abstract class Author extends Action
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
     * date filter
     *
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

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
        Date $dateFilter,
        Context $context

    )
    {
        $this->coreRegistry = $registry;
        $this->authorFactory = $authorFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->dateFilter = $dateFilter;
        parent::__construct($context);
    }

    /**
     * @return \Sample\News\Model\Author
     */
    protected function initAuthor()
    {
        $authorId  = (int) $this->getRequest()->getParam('author_id');
        /** @var \Sample\News\Model\Author $author */
        $author    = $this->authorFactory->create();
        if ($authorId) {
            $author->load($authorId);
        }
        $this->coreRegistry->register('sample_news_author', $author);
        return $author;
    }

    /**
     * filter dates
     *
     * @param array $data
     * @return array
     */
    public function filterData($data)
    {
        $inputFilter = new \Zend_Filter_Input(
            ['dob' => $this->dateFilter],
            [],
            $data
        );
        $data = $inputFilter->getUnescaped();
        if (isset($data['awards'])) {
            if (is_array($data['awards'])) {
                $data['awards'] = implode(',', $data['awards']);
            }
        }
        return $data;
    }

}
