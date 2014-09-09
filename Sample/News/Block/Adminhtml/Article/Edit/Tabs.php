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
namespace Sample\News\Block\Adminhtml\Article\Edit;

class Tabs
    extends \Magento\Backend\Block\Widget\Tabs {
    /**
     * @access protected
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('sample_news_article_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Article Information'));
    }
}
