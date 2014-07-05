<?php
namespace Sample\News\Block\Adminhtml;
class Article extends \Magento\Backend\Block\Widget\Grid\Container {
    /**
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