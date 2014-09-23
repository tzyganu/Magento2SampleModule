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
namespace Sample\News\Block\Adminhtml\Catalog\Category\Tab;

class Section
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface {
    /**
     * @var \Sample\News\Helper\Category
     */
    protected $_categoryHelper;

    /**
     * @param \Sample\News\Helper\Category $categoryHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Sample\News\Helper\Category $categoryHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = array()
    ) {
        $this->_categoryHelper = $categoryHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     * @return $this
     */
    protected function _prepareForm() {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->_coreRegistry->registry('current_category');
        $form   = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'=>__('Sections'),
            'class' => 'fieldset-wide')
        );
        $fieldset->addField('sample_news_sections_ids', '\Sample\News\Block\Adminhtml\Helper\Section', array(
            'name'  => 'sample_news_sections_ids',
            'label'     => __('Sections'),
            'title'     => __('Sections'),

        ));

        if (is_null($category->getSampleNewsSectionsIds())) {
            $sections = $this->_categoryHelper->getSelectedSections($category);
            $sectionIds = array();
            foreach ($sections as $section) {
                $sectionIds[] = $section->getId();
            }
            $category->setSampleNewsSectionsIds($sectionIds);
        }
        $form->addValues($category->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
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
