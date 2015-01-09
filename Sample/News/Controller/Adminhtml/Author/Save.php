<?php
namespace Sample\News\Controller\Adminhtml\Author;

use Sample\News\Controller\Adminhtml\Author;
use \Magento\Framework\Stdlib\DateTime\Filter\Date;
use \Sample\News\Model\AuthorFactory;
use \Magento\Backend\Model\Session;
use \Magento\Backend\App\Action\Context;
use \Magento\Backend\Model\View\Result\RedirectFactory;
use \Magento\Framework\Model\Exception as FrameworkException;
use \Sample\News\Model\Author\Image as ImageModel;
use \Sample\News\Model\Author\File as FileModel;
use \Sample\News\Model\Upload;

class Save extends Author
{
    /**
     * author factory
     * @var \Sample\News\Model\AuthorFactory
     */
    protected $authorFactory;

    /**
     * backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * date filter
     *
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $dateFilter;

    /**
     * image model
     *
     * @var \Sample\News\Model\Author\Image
     */
    protected $imageModel;

    /**
     * file model
     *
     * @var \Sample\News\Model\Author\File
     */
    protected $fileModel;

    /**
     * upload model
     *
     * @var \Sample\News\Model\Upload
     */
    protected $uploadModel;


    /**
     * @param AuthorFactory $authorFactory
     * @param Session $backendSession
     * @param Date $dateFilter
     * @param ImageModel $imageModel
     * @param FileModel $fileModel
     * @param Upload $uploadModel
     * @param Context $context
     * @param RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        AuthorFactory $authorFactory,
        Session $backendSession,
        Date $dateFilter,
        ImageModel $imageModel,
        FileModel $fileModel,
        Upload $uploadModel,
        Context $context,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->authorFactory = $authorFactory;
        $this->backendSession = $backendSession;
        $this->dateFilter = $dateFilter;
        $this->imageModel = $imageModel;
        $this->fileModel = $fileModel;
        $this->uploadModel = $uploadModel;
        parent::__construct($context, $resultRedirectFactory);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('author');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->filterData($data);
            $author = $this->authorFactory->create();

            $id = $this->getRequest()->getParam('author_id');
            if ($id) {
                $author->load($id);
            }
            $author->setData($data);
            $avatar = $this->uploadModel->uploadFileAndGetName('avatar', $this->imageModel->getBaseDir(), $data);
            $author->setAvatar($avatar);
            $resumee = $this->uploadModel->uploadFileAndGetName('resumee', $this->fileModel->getBaseDir(), $data);
            $author->setResumee($resumee);
            $this->_eventManager->dispatch(
                'sample_news_author_prepare_save',
                [
                    'author' => $author,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $author->save();
                $this->messageManager->addSuccess(__('The author has been saved.'));
                $this->backendSession->setSampleNewsAuthorData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'sample_news/*/edit',
                        [
                            'author_id' => $author->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('sample_news/*/');
                return $resultRedirect;
            } catch (FrameworkException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the author.'));
            }

            $this->_getSession()->setSampleNewsAuthorData($data);
            $resultRedirect->setPath(
                'sample_news/*/edit',
                [
                    'author_id' => $author->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        $resultRedirect->setPath('sample_news/*/');
        return $resultRedirect;
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
            array('dob' => $this->dateFilter),
            array(),
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
