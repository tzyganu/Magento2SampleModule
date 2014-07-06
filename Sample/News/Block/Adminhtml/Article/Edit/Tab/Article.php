<?php
namespace Sample\News\Block\Adminhtml\Article\Edit\Tab;

/**
 * Adminhtml cms block edit form
 */
class Article extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_wysiwygConfig;
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = array()
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('article_form');
        $this->setTitle(__('Article Information'));
    }

    /**
     * Load Wysiwyg on demand and Prepare layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->_wysiwygConfig->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $article = $this->_coreRegistry->registry('sample_news_article');

        $form   = $this->_formFactory->create();

        $form->setHtmlIdPrefix('article_');
        $form->setFieldNameSuffix('article');


        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information'), 'class' => 'fieldset-wide'));

        if ($article->getId()) {
            $fieldset->addField('entity_id', 'hidden', array(
                'name' => 'entity_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => __('Title'),
            'title'     => __('Title'),
            'required'  => true,
        ));

        $fieldset->addField('identifier', 'text', array(
            'name'      => 'identifier',
            'label'     => __('Identifier'),
            'title'     => __('Identifier'),
            'required'  => true,
            'class'     => 'validate-xml-identifier',
        ));

        /* Check is single store mode */
        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => $this->_storeManager->getStore(true)->getId()
            ));
            $article->setStoreId($this->_storeManager->getStore(true)->getId());
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
        if (!$article->getId()) {
            $article->setData('status', '1');
        }

        $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => __('Content'),
            'title'     => __('Content'),
            'style'     => 'height:36em',
            'required'  => true,
            'config'    => $this->_wysiwygConfig->getConfig()
        ));

        $fieldset->addField('categories_ids', '\Sample\News\Block\Adminhtml\Article\Helper\Category', array(
            'name'  => 'categories_ids',
            'label'     => __('Categories'),
            'title'     => __('Categories'),
        ));

        $articleData = $this->_session->getSampleNewsArticleData(true);
        if ($articleData) {
            $article->addData($articleData);
        }
        if (is_null($article->getCategoriesIds())) {
            $article->setCategoriesIds($article->getCategoryIds());
        }
        $form->setValues($article->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     * @access public
     * @return string
     */
    public function getTabLabel()
    {
        return __('Article');
    }

    /**
     * Prepare title for tab
     * @access public
     * @return string
     */
    public function getTabTitle()
    {
        return __('Article');
    }

    /**
     * Can show tab in tabs
     * @access public
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     * @access public
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
