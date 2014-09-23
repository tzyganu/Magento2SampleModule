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
namespace Sample\News\Block\Adminhtml\Article;

class Edit
    extends \Magento\Backend\Block\Widget\Form\Container {
    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct() {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Sample_News';
        $this->_controller = 'adminhtml_article';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Article'));
        $this->buttonList->update('delete', 'label', __('Delete Article'));

        $this->buttonList->add('saveandcontinue', array(
            'label'     => __('Save and Continue Edit'),
            'class'     => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), -100);
    }

    /**
     * Get edit form container header text
     * @return string
     */
    public function getHeaderText() {
        if ($this->_coreRegistry->registry('sample_news_article')->getId()) {
            return __("Edit Article '%1'", $this->escapeHtml($this->_coreRegistry->registry('sample_news_article')->getTitle()));
        } else {
            return __('New Article');
        }
    }
}
