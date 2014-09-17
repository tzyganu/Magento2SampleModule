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
class Category
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface {
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     * @access public
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('article_form');
        $this->setTitle(__('Article Information'));
    }
    /**
     * Prepare form
     * @access protected
     * @return $this
     */
    protected function _prepareForm()
    {
        $article = $this->_coreRegistry->registry('sample_news_article');
        $form   = $this->_formFactory->create();
        $form->setHtmlIdPrefix('article_');
        $form->setFieldNameSuffix('article');
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Categories'), 'class' => 'fieldset-wide'));
        $fieldset->addField('categories_ids', '\Sample\News\Block\Adminhtml\Helper\Category', array(
            'name'  => 'categories_ids',
            'label'     => __('Categories'),
            'title'     => __('Categories'),

        ));

        if (is_null($article->getCategoriesIds())) {
            $article->setCategoriesIds($article->getCategoryIds());
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
        return __('Associated Categories');
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
