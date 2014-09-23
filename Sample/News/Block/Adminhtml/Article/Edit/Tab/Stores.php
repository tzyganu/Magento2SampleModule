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

class Stores
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface {
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareForm() {
        /** @var \Sample\News\Model\Article $article */
        $article = $this->_coreRegistry->registry('sample_news_article');
        $form   = $this->_formFactory->create();
        $form->setHtmlIdPrefix('article_');
        $form->setFieldNameSuffix('article');
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'=>__('Stores'),
            'class' => 'fieldset-wide')
        );
        $field = $fieldset->addField('store_id', 'multiselect', array(
            'name'      => 'stores[]',
            'label'     => __('Store View'),
            'title'     => __('Store View'),
            'required'  => true,
            'values'    => $this->_systemStore->getStoreValuesForForm(false, true),
        ));
        /** @var \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element $renderer */
        $renderer = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
        $field->setRenderer($renderer);

        $form->addValues($article->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     * @return string
     */
    public function getTabLabel() {
        return __('Stores');
    }

    /**
     * Prepare title for tab
     * @return string
     */
    public function getTabTitle() {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     * @return boolean
     */
    public function canShowTab() {
        return !$this->_storeManager->isSingleStoreMode();
    }

    /**
     * Tab is hidden
     * @return boolean
     */
    public function isHidden() {
        return false;
    }
}
