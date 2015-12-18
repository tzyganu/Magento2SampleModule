<?php
namespace Sample\News\Block\Adminhtml\Author\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * @method Tabs setTitle(\string $title)
 */
class Tabs extends WidgetTabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('author_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Author Information'));
    }
}
