<?php
namespace Sample\News\Block\Adminhtml\Author\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use \Magento\Backend\Block\Widget\Tab\TabInterface;
use \Magento\Store\Model\System\Store;
use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Data\FormFactory;

class Stores extends GenericForm implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * constructor
     *
     * @param Store $systemStore
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Store $systemStore,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare the form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Sample\News\Model\Author $author */
        $author = $this->_coreRegistry->registry('sample_news_author');
        $form   = $this->_formFactory->create();
        $form->setHtmlIdPrefix('author_');
        $form->setFieldNameSuffix('author');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend'    =>__('Stores'),
                'class'     => 'fieldset-wide'
            ]
        );
        $field = $fieldset->addField(
            'store_id',
            'multiselect',
            [
                'name'      => 'stores[]',
                'label'     => __('Store View'),
                'title'     => __('Store View'),
                'required'  => true,
                'values'    => $this->systemStore->getStoreValuesForForm(false, true),
            ]
        );
        /** @var \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element $renderer */
        $renderer = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
        $field->setRenderer($renderer);
        $form->addValues($author->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Stores');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return !$this->_storeManager->isSingleStoreMode();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
