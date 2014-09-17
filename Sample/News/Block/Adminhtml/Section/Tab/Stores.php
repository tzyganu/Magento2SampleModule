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
namespace Sample\News\Block\Adminhtml\Section\Tab;

class Stores
    extends \Magento\Backend\Block\Widget\Form\Generic {
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @access protected
     * @return $this
     */
    protected function _prepareForm() {
        $model = $this->_coreRegistry->registry('sample_news_section');
        $form   = $this->_formFactory->create();
        $form->setHtmlIdPrefix('section_');
        $form->setFieldNameSuffix('section');
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Stores'), 'class' => 'fieldset-wide'));
        $field = $fieldset->addField('store_id', 'multiselect', array(
            'name'      => 'stores[]',
            'label'     => __('Store View'),
            'title'     => __('Store View'),
            'required'  => true,
            'values'    => $this->_systemStore->getStoreValuesForForm(false, true),
        ));
        $renderer = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
        $field->setRenderer($renderer);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
