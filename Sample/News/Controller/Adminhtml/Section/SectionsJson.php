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

class SectionsJson extends \Sample\News\Controller\Adminhtml\Section
{
    /**
     * Get tree node (Ajax version)
     *
     * @return void
     */
    public function execute() {
        if ($this->getRequest()->getParam('expand_all')) {
            $this->_authSession->setIsSampleNewsSectionTreeWasExpanded(true);
        } else {
            $this->_authSession->setIsSampleNewsSectionTreeWasExpanded(false);
        }
        $sectionId = (int)$this->getRequest()->getPost('id');
        if ($sectionId) {
            $this->getRequest()->setParam('id', $sectionId);

            if (!($section = $this->_initSection())) {
                return;
            }
            $this->getResponse()->representJson(
                $this->_view->getLayout()->createBlock('Sample\News\Block\Adminhtml\Section\Tree')
                    ->getTreeJson($section)
            );
        }
    }
}
