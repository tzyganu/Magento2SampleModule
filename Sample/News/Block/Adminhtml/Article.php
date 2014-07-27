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
namespace Sample\News\Block\Adminhtml;
class Article
    extends \Magento\Backend\Block\Widget\Grid\Container {
    /**
     * @access protected
     * @return void
     */
    protected function _construct() {
        $this->_blockGroup = 'Sample_News';
        $this->_controller = 'adminhtml_article';
        $this->_headerText = __('Articles');
        $this->_addButtonLabel = __('Add New Article');
        parent::_construct();
    }
}
