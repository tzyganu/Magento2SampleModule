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

class Edit
    extends \Sample\News\Controller\Adminhtml\Article {
    /**
     * edit article
     *
     * @return void
     */
    public function execute() {
        $this->_title->add(__('Articles'));
        $article = $this->_initArticle();
        $this->_title->add($article->getId() ? $article->getTitle() : __('New Article'));
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $article->setData($data);
        }
        $this->_initAction()->_addBreadcrumb(
            $article->getId() ? __('Edit Article') : __('New Article'),
            $article->getId() ? __('Edit Article') : __('New Article')
        );
        $this->_view->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_view->renderLayout();
    }
}
