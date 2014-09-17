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

class Delete extends \Sample\News\Controller\Adminhtml\Section
{
    /**
     * Delete section action
     *
     * @return void
     */
    public function execute()
    {
        $sectionId = (int)$this->getRequest()->getParam('id');
        if ($sectionId) {
            try {
                $section = $this->_objectManager->create('Sample\News\Model\Section')->load($sectionId);
                $this->_eventManager->dispatch('sample_news_controller_category_delete', array('section' => $section));

                $this->_authSession->setSampleNewsSectionDeletedPath($section->getPath());

                $section->delete();
                $this->messageManager->addSuccess(__('The section was deleted.'));
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('sample_news/*/edit', array('_current' => true)));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while trying to delete the section.'));
                $this->getResponse()->setRedirect($this->getUrl('sample_news/*/edit', array('_current' => true)));
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('sample_news/*/', array('_current' => true, 'id' => null)));
    }
}
