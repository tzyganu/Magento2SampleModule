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
class Section
    extends \Magento\Backend\Block\Widget\Form\Generic {

    /**
     * Prepare form
     * @access protected
     * @return $this
     */
    protected function _prepareForm() {
        /** @var \Sample\News\Model\Section $section */
        $section = $this->_coreRegistry->registry('sample_news_section');
        $form   = $this->_formFactory->create();
        $form->setHtmlIdPrefix('section_');
        $form->setFieldNameSuffix('section');
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'=>__('Section Information'),
            'class' => 'fieldset-wide')
        );
        if (!$section->getId()) {
            // path
            if ($this->getRequest()->getParam('parent')) {
                $fieldset->addField('path', 'hidden', array(
                    'name' => 'path',
                    'value' => $this->getRequest()->getParam('parent')
                ));
            } else {
                $fieldset->addField('path', 'hidden', array('name' => 'path', 'value' => 1));
            }
        } else {
            $fieldset->addField('id', 'hidden', array('name' => 'id', 'value' => $section->getId()));
            $fieldset->addField('path', 'hidden', array(
                'name' => 'path',
                'value' => $section->getPath()
            ));
        }
        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => __('Name'),
            'title'     => __('name'),
            'required'  => true,
        ));
        $fieldset->addField('identifier', 'text', array(
            'name'      => 'identifier',
            'label'     => __('Identifier'),
            'title'     => __('Identifier'),
        ));

        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => $this->_storeManager->getStore(true)->getId()
            ));
            $section->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField('status', 'select', array(
            'label'     => __('Status'),
            'title'     => __('Status'),
            'name'      => 'status',
            'required'  => true,
            'options'   => array(
                '1' => __('Enabled'),
                '0' => __('Disabled'),
            ),
        ));
        $fieldset->addField('in_rss', 'select', array(
            'label'     => __('Show in RSS'),
            'title'     => __('Show in RSS'),
            'name'      => 'in_rss',
            'required'  => true,
            'options'   => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ),
        ));
        //TODO: move these to default values
        if (!$section->getId()) {
            $section->setData('status', '1');
            $section->setData('in_rss', 1);
        }
        $sectionData = $this->_session->getSampleNewsSectionData(true);
        if ($sectionData) {
            $section->addData($sectionData);
        }
        else {
            if (!$section->getId()) {
                $section->addData($section->getDefaultValues());
            }
        }
        if (is_null($section->getCategoriesIds())) {
            $section->setCategoriesIds($section->getCategoryIds());
        }
        $form->addValues($section->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
