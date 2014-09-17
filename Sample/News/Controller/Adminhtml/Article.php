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
namespace Sample\News\Controller\Adminhtml;
class Article extends \Magento\Backend\App\Action {
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return $this
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Sample_News::news_article'
        )->_addBreadcrumb(
            __('News'),
            __('News')
        )->_addBreadcrumb(
            __('Articles'),
            __('Articles')
        );
        return $this;
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Sample_News::news_article');
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
}
