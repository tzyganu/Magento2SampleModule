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

class RefreshPath extends \Sample\News\Controller\Adminhtml\Section
{
    /**
     * Build response for refresh input element 'path' in form
     *
     * @return void
     */
    public function execute()
    {
        $sectionId = (int)$this->getRequest()->getParam('id');
        if ($sectionId) {
            $section = $this->_objectManager->create('Sample\News\Model\Section')->load($sectionId);
            $this->getResponse()->representJson(
                $this->_objectManager->get(
                    'Magento\Core\Helper\Data'
                )->jsonEncode(
                    array('id' => $sectionId, 'path' => $section->getPath())
                )
            );
        }
    }
}
