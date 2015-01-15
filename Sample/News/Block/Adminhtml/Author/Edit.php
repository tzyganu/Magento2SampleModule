<?php
namespace Sample\News\Block\Adminhtml\Author;

use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends FormContainer
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    )
    {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize author edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'author_id';
        $this->_blockGroup = 'Sample_News';
        $this->_controller = 'adminhtml_author';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Author'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete Author'));
    }

    /**
     * Retrieve text for header element depending on loaded author
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \Sample\News\Model\Author $author */
        $author = $this->coreRegistry->registry('sample_news_author');
        if ($author->getId()) {
            return __("Edit Author '%1'", $this->escapeHtml($author->getName()));
        }
        return __('New Author');
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('author_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'author_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'author_content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
}
