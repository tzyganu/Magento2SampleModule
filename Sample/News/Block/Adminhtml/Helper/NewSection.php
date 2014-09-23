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
namespace Sample\News\Block\Adminhtml\Helper;

class NewSection
    extends \Magento\Backend\Block\Widget\Form\Generic {
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_sectionFactory;

    /**
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Sample\News\Model\SectionFactory $sectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Sample\News\Model\SectionFactory $sectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_sectionFactory = $sectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
        $this->setUseContainer(true);
    }

    /**
     * Form preparation
     * @return void
     */
    protected function _prepareForm() {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(array('data' => array('id' => 'new_section_form')));
        $form->setUseContainer($this->getUseContainer());
        $form->addField('new_section_messages', 'note', array());
        $fieldset = $form->addFieldset('new_section_form_fieldset', array());
        $fieldset->addField(
            'new_section_name',
            'text',
            array(
                'label' => __('Section Name'),
                'title' => __('Section Name'),
                'required' => true,
                'name' => 'new_section_name'
            )
        );
        $fieldset->addField(
            'new_section_parent',
            'select',
            array(
                'label' => __('Parent Section'),
                'title' => __('Parent Section'),
                'required' => true,
                'options' => $this->_getParentSectionOptions(),
                'class' => 'validate-parent-section',
                'name' => 'new_section_parent',
            )
        );
        //TODO: add mandatory fields in here
        $this->setForm($form);
    }

    /**
     * Get parent section options
     * @return array
     */
    protected function _getParentSectionOptions() {
        $items = $this->_sectionFactory->create()
            ->getResourceCollection()
            ->addOrder('entity_id', 'ASC')
            ->setPageSize(2)
            ->load()
            ->getItems();

        $result = array();
        if (count($items) === 1) {
            $item = array_pop($items);
            $result = array($item->getEntityId() => $item->getName());
        }
        return $result;
    }

    /**
     * Section save action URL
     * @return string
     */
    public function getSaveSectionUrl() {
        return $this->getUrl('sample_news/section/save');
    }

    /**
     * Attach new section dialog widget initialization
     * @return string
     */
    public function getAfterElementHtml() {
        $widgetOptions = $this->_jsonEncoder->encode(
            array(
                'suggestOptions' => array(
                    'source' => $this->getUrl('sample_news/section/suggestSections'),
                    'valueField' => '#new_section_parent',
                    'className' => 'section-select',
                    'multiselect' => true,
                    'showAll' => true
                ),
                'saveSectionUrl' => $this->getUrl('sample_news/section/save')
            )
        );
        //TODO: JavaScript logic should be moved to separate file or reviewed
        return <<<HTML
            <script type="text/javascript">
                require(["jquery","mage/mage", "Sample_News/js/new-section-dialog"],function($) {  // waiting for dependencies at first
                    $(function(){ // waiting for page to load to have '#category_ids-template' available
                        $('#new-section').newSectionDialog($widgetOptions);
                    });
                });
            </script>
HTML;
    }
}
