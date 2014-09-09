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
namespace Sample\News\Block\Adminhtml\Article\Edit\Tab;

class Meta
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface {
    /**
     * Init form
     * @access protected
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('article_form');
    }

    /**
     * Prepare form
     * @access protected
     * @return Meta
     */
    protected function _prepareForm() {
        $article = $this->_coreRegistry->registry('sample_news_article');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('article_');
        $form->setFieldNameSuffix('article');
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Meta Information'), 'class' => 'fieldset-wide'));
        $fieldset->addField('meta_title', 'text', array(
            'name'      => 'meta_title',
            'label'     => __('Meta Title'),
            'title'     => __('Meta Title'),
            'required'  => false,
        ));
        $fieldset->addField('meta_keywords', 'textarea', array(
            'name'      => 'meta_keywords',
            'label'     => __('Meta Keywords'),
            'title'     => __('Meta Keywords'),
            'required'  => false,
            'rows'       => 5
        ));
        $fieldset->addField('meta_description', 'textarea', array(
            'name'      => 'meta_description',
            'label'     => __('Meta Description'),
            'title'     => __('Meta Description'),
            'required'  => false,
            'rows'       => 5
        ));
        $form->setValues($article->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     * @access public
     * @return string
     */
    public function getTabLabel() {
        return __('Meta');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     * @access public
     * @return boolean
     */
    public function canShowTab() {
        return true;
    }

    /**
     * Tab is hidden
     * @access public
     * @return boolean
     */
    public function isHidden() {
        return false;
    }
}
