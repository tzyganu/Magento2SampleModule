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

    /**
     * @access protected
     * @return $this
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $this->addTab('form_article', array(
            'label'        => __('Article'),
            'title'        => __('Article'),
            'content'      => $this->getLayout()->createBlock('Sample\News\Block\Adminhtml\Article\Edit\Tab\Article')->toHtml(),
        ));
        $this->addTab('article_meta', array(
            'label'        => __('Meta Information'),
            'title'        => __('Meta Information'),
            'content'      => $this->getLayout()->createBlock('Sample\News\Block\Adminhtml\Article\Edit\Tab\Meta')->toHtml(),
        ));
        $this->addTab('article_stores', array(
            'label'        => __('Stores'),
            'title'        => __('Stores'),
            'content'      => $this->getLayout()->createBlock('Sample\News\Block\Adminhtml\Article\Edit\Tab\Stores')->toHtml(),
        ));
        $this->addTab('catalog_products', array(
            'label'        => __('Products'),
            'url'          => $this->getUrl('sample_news/*/products', array('_current' => true)),
            'class'        => 'ajax',
        ));
        return $this;
    }
}
