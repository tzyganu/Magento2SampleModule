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
class Category
    extends \Magento\Backend\Block\Widget\Form\Generic {

    /**
     * Init form
     * @access public
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('section_form');
        $this->setTitle(__('Section Information'));
    }
    /**
     * Prepare form
     * @access protected
     * @return $this
     */
    protected function _prepareForm()
    {
        $section = $this->_coreRegistry->registry('sample_news_section');
        $form   = $this->_formFactory->create();
        $form->setHtmlIdPrefix('section_');
        $form->setFieldNameSuffix('section');
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Categories'), 'class' => 'fieldset-wide'));
        $fieldset->addField('categories_ids', '\Sample\News\Block\Adminhtml\Helper\Category', array(
            'name'  => 'categories_ids',
            'label'     => __('Categories'),
            'title'     => __('Categories'),

        ));

        if (is_null($section->getCategoriesIds())) {
            $section->setCategoriesIds($section->getCategoryIds());
        }
        $form->addValues($section->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
