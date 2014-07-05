<?php
namespace Sample\News\Block\Adminhtml\Article;

/**
 * CMS block edit form container
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Sample_News';
        $this->_controller = 'adminhtml_article';

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save Article'));
        $this->_updateButton('delete', 'label', __('Delete Article'));

        $this->_addButton('saveandcontinue', array(
            'label'     => __('Save and Continue Edit'),
            'class'     => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), -100);

//        $this->_formScripts[] = "
//            function toggleEditor() {
//                if (tinyMCE.getInstanceById('block_content') == null) {
//                    tinyMCE.execCommand('mceAddControl', false, 'block_content');
//                } else {
//                    tinyMCE.execCommand('mceRemoveControl', false, 'block_content');
//                }
//            }
//        ";
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('sample_news_article')->getId()) {
            return __("Edit Article '%1'", $this->escapeHtml($this->_coreRegistry->registry('sample_news_article')->getTitle()));
        } else {
            return __('New Article');
        }
    }
}
