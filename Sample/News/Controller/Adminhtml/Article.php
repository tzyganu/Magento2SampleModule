<?php
namespace Sample\News\Controller\Adminhtml;
class Article extends \Magento\Backend\App\Action{
    protected $_coreRegistry = null;
    protected $_jsHelper = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_jsHelper = $jsHelper;
        parent::__construct($context);
    }
    public function indexAction(){
        $this->_view->loadLayout();
        $this->_title->add(__('News'))->add(__('Article'));
        $this->_setActiveMenu('Sample_News::sample_news')
            ->_addBreadcrumb(__('Article'), __('Article'));
        $this->_view->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    protected function _initArticle() {
        $articleId  = (int) $this->getRequest()->getParam('id');
        $article    = $this->_objectManager->create('Sample\News\Model\Article');
        if ($articleId) {
            $article->load($articleId);
        }
        $this->_coreRegistry->register('sample_news_article', $article);
        return $article;

    }

    /**
     * Edit CMS block
     *
     * @return void
     */
    public function editAction()
    {
        $articleId  = (int) $this->getRequest()->getParam('id');
        $this->_title->add(__('Articles'));
        $article = $this->_initArticle();
        $this->_title->add($article->getId() ? $article->getTitle() : __('New Article'));
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (! empty($data)) {
            $article->setData($data);
        }
        $this->_view->loadLayout();
        $this->_setActiveMenu('Sample_News::sample_news')
            ->_addBreadcrumb(__('Article'), __('Article'))
            ->_addBreadcrumb($articleId ? __('Edit Article') : __('New Article'), $articleId ? __('Edit article') : __('New Article'));
        $this->_view->renderLayout();
    }

    public function saveAction()
    {
        $data = $this->getRequest()->getPost('article');
        if ($data) {
            $article = $this->_objectManager->create('Sample\News\Model\Article');
            $id = $this->getRequest()->getParam('entity_id');
            if ($id) {
                $article->load($id);
            }
            $article->setData($data);
            $products = $this->getRequest()->getPost('products', -1);
            if ($products != -1) {
                $article->setProductsData($this->_jsHelper->decodeGridSerializedInput($products));
            }
            $this->_eventManager->dispatch('sample_news_article_prepare_save', array('article' => $article, 'request' => $this->getRequest()));
            try {
                $article->save();
                $this->messageManager->addSuccess(__('The article has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setSampleNewsArticleData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $article->getId(), '_current'=>true));
                    return;
                }
                $this->_redirect('*/*/');
                return;

            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_session->setSampleNewsArticleData($data);
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the article.'));
                $this->_session->setSampleNewsArticleData($data);
            }

            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
        $this->_redirect('*/*/');
    }
    public function gridAction(){
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    public function massStatusAction(){
        $articleIds = (array)$this->getRequest()->getParam('entity_ids');
        $status     = (int)$this->getRequest()->getParam('status');

        try {
            foreach ($articleIds as $id){
                $article = $this->_objectManager->get('Sample\News\Model\Article')
                    ->load($id);
                if ($article->getId()) {
                    $article->setStatus($status)->save();
                }
            }

            $this->messageManager->addSuccess(
                __('A total of %1 record(s) have been updated.', count($articleIds))
            );
        } catch (\Magento\Core\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()
                ->addException($e, __('Something went wrong while updating the article(s) status.'));
        }

        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                // init model and delete
                $article = $this->_objectManager->create('Sample\News\Model\Article');
                $article->load($id);
                $article->delete();
                // display success message
                $this->messageManager->addSuccess(__('The article has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a article to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    public function productsAction() {

        $this->_initArticle();
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('article.edit.tab.product')
            ->setArticleProducts($this->getRequest()->getPost('article_products', null));
        $this->_view->renderLayout();
    }
    public function productsgridAction() {

        $this->_initArticle();
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('article.edit.tab.product')
            ->setArticleProducts($this->getRequest()->getPost('article_products', null));
        $this->_view->renderLayout();
    }
}