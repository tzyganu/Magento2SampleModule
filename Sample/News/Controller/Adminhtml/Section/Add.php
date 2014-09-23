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

class Add extends \Sample\News\Controller\Adminhtml\Section
{
    /**
     * Add new section form
     *
     * @return void
     */
    public function execute() {
        $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->unsSampleNewsSectionActiveTabId();
        $this->_forward('edit');
    }
}
