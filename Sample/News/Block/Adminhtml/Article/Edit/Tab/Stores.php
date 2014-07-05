<?php

namespace Sample\News\Block\Adminhtml\Article\Edit\Tab;

/**
 * Adminhtml cms block edit form
 */
class Stores
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Init form
     *
     * @return void
     */

    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }


//    protected function _construct()
//    {
//        parent::_construct();
//        $this->setId('stores_form');
//        $this->setTitle(__('Stores'));
//    }
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('sample_news_article');
        /** @var \Magento\Data\Form $form */
        $form   = $this->_formFactory->create();
        $form->setHtmlIdPrefix('article_');
        $form->setFieldNameSuffix('article');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Stores'), 'class' => 'fieldset-wide'));
        /* Check is single store mode */
        $field =$fieldset->addField('store_id', 'multiselect', array(
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

    /**
     * Prepare label for tab
     * @access public
     * @return string
     */
    public function getTabLabel()
    {
        return __('Stores');
    }

    /**
     * Prepare title for tab
     * @access public
     * @return string
     */
    public function getTabTitle()
    {
        return __('Stores');
    }

    /**
     * Can show tab in tabs
     * @access public
     * @return boolean
     */
    public function canShowTab()
    {
        return !$this->_storeManager->isSingleStoreMode();
    }

    /**
     * Tab is hidden
     * @access public
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}