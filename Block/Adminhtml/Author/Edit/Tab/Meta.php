<?php
namespace Sample\News\Block\Adminhtml\Author\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Meta extends GenericForm implements TabInterface
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Sample\News\Model\Author $author */
        $author = $this->_coreRegistry->registry('sample_news_author');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('author_');
        $form->setFieldNameSuffix('author');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend'    =>__('Meta Information'),
                'class'     => 'fieldset-wide'
            ]
        );
        $fieldset->addField(
            'meta_title',
            'text',
            [
                'name'      => 'meta_title',
                'label'     => __('Meta Title'),
                'title'     => __('Meta Title'),
                'required'  => false,
            ]
        );
        $fieldset->addField(
            'meta_keywords',
            'textarea',
            [
                'name'      => 'meta_keywords',
                'label'     => __('Meta Keywords'),
                'title'     => __('Meta Keywords'),
                'required'  => false,
                'rows'      => 5
            ]
        );
        $fieldset->addField(
            'meta_description',
            'textarea',
            [
                'name'      => 'meta_description',
                'label'     => __('Meta Description'),
                'title'     => __('Meta Description'),
                'required'  => false,
                'rows'      => 5
            ]
        );
        $form->setValues($author->getData());
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
        return __('Meta');
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
        return true;
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
