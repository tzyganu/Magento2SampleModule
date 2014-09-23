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
class Section
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface {
    /**
     * Prepare form
     * @return $this
     */
    protected function _prepareForm() {
        /** @var \Sample\News\Model\Article $article */
        $article = $this->_coreRegistry->registry('sample_news_article');
        $form   = $this->_formFactory->create();
        $form->setHtmlIdPrefix('article_');
        $form->setFieldNameSuffix('article');
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'=>__('Sections'),
            'class' => 'fieldset-wide')
        );
        $fieldset->addField('sections_ids', '\Sample\News\Block\Adminhtml\Helper\Section', array(
            'name'  => 'sections_ids',
            'label'     => __('Sections'),
            'title'     => __('Sections'),

        ));

        if (is_null($article->getSectionsIds())) {
            $article->setSectionsIds($article->getSectionIds());
        }
        $form->addValues($article->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     * @access public
     * @return string
     */
    public function getTabLabel() {
        return __('Sections');
    }

    /**
     * Prepare title for tab
     * @access public
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
