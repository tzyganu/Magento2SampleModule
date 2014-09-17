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
namespace Sample\News\Controller\Adminhtml\Section;

class Edit extends \Sample\News\Controller\Adminhtml\Section {
    /**
     * Edit section page
     *
     * @return void
     */
    public function execute() {
        $parentId = (int)$this->getRequest()->getParam('parent');
        $sectionId = (int)$this->getRequest()->getParam('id');

//        if (!$sectionId && !$parentId) {
//            $this->getRequest()->setParam('id', (int)$this->_sectionHelper->getRootSectionId());
//        }

        $section = $this->_initSection();
        if (!$section) {
            return;
        }

        $this->_title->add($sectionId ? $section->getName() : __('Sections'));

        /**
         * Check if we have data in session (if during category save was exception)
         */
        $data = $this->_getSession()->getSampleNewsSectionData(true);
        if (isset($data['section'])) {
            $section->addData($data['section']);
        }

        /**
         * Build response for ajax request
         */
        if ($this->getRequest()->getQuery('isAjax')) {
            // prepare breadcrumbs of selected category, if any
            $breadcrumbsPath = $section->getPath();
            if (empty($breadcrumbsPath)) {
                $breadcrumbsPath = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')
                    ->getSampleNewsSectionDeletedPath(true);
                if (!empty($breadcrumbsPath)) {
                    $breadcrumbsPath = explode('/', $breadcrumbsPath);
                    if (count($breadcrumbsPath) <= 1) {
                        $breadcrumbsPath = '';
                    } else {
                        array_pop($breadcrumbsPath);
                        $breadcrumbsPath = implode('/', $breadcrumbsPath);
                    }
                }
            }

            $this->_view->loadLayout();

            $eventResponse = new \Magento\Framework\Object(
                array(
                    'content' => $this->_view->getLayout()->getBlock(
                        'sample_news.section.edit'
                    )->getFormHtml() . $this->_view->getLayout()->getBlock(
                        'sample_news.section.tree'
                    )->getBreadcrumbsJavascript(
                        $breadcrumbsPath,
                        'editingSectionBreadcrumbs'
                    ),
                    'messages' => $this->_view->getLayout()->getMessagesBlock()->getGroupedHtml()
                )
            );
            $this->_eventManager->dispatch(
                'sample_news_section_prepare_ajax_response',
                array('response' => $eventResponse, 'controller' => $this)
            );
            $this->getResponse()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($eventResponse->getData())
            );
            return;
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu('Sample_news::news_section');
        $this->_view->getLayout()->getBlock('head')->setCanLoadExtJs(true)->setContainerCssClass('sample-news-sections');

        $this->_addBreadcrumb(__('Manage Sections'), __('Manage Sections'));

        $block = $this->_view->getLayout()->getBlock('catalog.wysiwyg.js');

        $this->_view->renderLayout();
    }
}
