<?php
namespace Sample\News\Controller\Adminhtml\Author;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\View\Result\PageFactory;

//TODO: make a common ancestor for all exports
class ExportExcel extends Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param FileFactory $fileFactory
     * @param PageFactory $resultPageFactory
     * @param Context $context
     */
    public function __construct(
        FileFactory $fileFactory,
        PageFactory $resultPageFactory,
        Context $context
    )
    {
        $this->fileFactory = $fileFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Export shipment grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $fileName = 'authors.xml';
        /** @var \Sample\News\Block\Adminhtml\Author\Grid $grid */
        $grid = $resultPage->getLayout()->getChildBlock('sample_news.author.grid', 'grid.export');
        return $this->fileFactory->create(
            $fileName,
            $grid->getExcelFile(),
            DirectoryList::VAR_DIR
        );
    }
}
