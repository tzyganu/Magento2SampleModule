<?php
/**
 * Sample_News extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Sample
 * @package        Sample_News
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Sample\News\Controller\Adminhtml\Article;

class Save
    extends \Sample\News\Controller\Adminhtml\Article {
    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $_jsHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Helper\Js $jsHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Helper\Js $jsHelper
    ) {
        $this->_jsHelper = $jsHelper;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * save action
     */
    public function execute() {
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
}
