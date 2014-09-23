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
class SuggestSections extends \Sample\News\Controller\Adminhtml\Section
{
    /**
     * Category list suggestion based on already entered symbols
     *
     * @return void
     */
    public function execute() {
        $this->getResponse()->representJson(
            $this->_view->getLayout()->createBlock(
                'Sample\News\Block\Adminhtml\Section\Tree'
            )->getSuggestedSectionsJson(
                $this->getRequest()->getParam('label_part')
            )
        );
    }
}
