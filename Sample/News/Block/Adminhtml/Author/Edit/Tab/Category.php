<?php
namespace Sample\News\Block\Adminhtml\Author\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;
class Category extends GenericForm implements TabInterface
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
        $form   = $this->_formFactory->create();
        $form->setHtmlIdPrefix('author_');
        $form->setFieldNameSuffix('author');
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'=>__('Categories'),
            'class' => 'fieldset-wide')
        );
        $fieldset->addField('categories_ids', '\Sample\News\Block\Adminhtml\Helper\Category', array(
            'name'  => 'categories_ids',
            'label'     => __('Categories'),
            'title'     => __('Categories'),

        ));

        if (is_null($author->getCategoriesIds())) {
            $author->setCategoriesIds($author->getCategoryIds());
        }
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
        return __('Categories');
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
