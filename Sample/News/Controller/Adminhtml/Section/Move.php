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

class Move extends \Sample\News\Controller\Adminhtml\Section
{
    /**
     * Move section action
     *
     * @return void
     */
    public function execute()
    {
        $section = $this->_initSection();
        if (!$section) {
            $this->getResponse()->setBody(__('There was a section move error.'));
            return;
        }
        /**
         * New parent section identifier
         */
        $parentNodeId = $this->getRequest()->getPost('pid', false);
        /**
         * Section id after which we have put our category
         */
        $prevNodeId = $this->getRequest()->getPost('aid', false);

        try {
            $section->move($parentNodeId, $prevNodeId);
            $this->getResponse()->setBody('SUCCESS');
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        } catch (\Exception $e) {
            $this->getResponse()->setBody(__('There was a section move error %1', $e));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
    }
}
