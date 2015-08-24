<?php
namespace Sample\News\Block\Adminhtml\Author\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Sample\News\Model\Author\Source\Award;
use Sample\News\Model\Author\Source\Type;
use Sample\News\Model\Source\Country;
use Magento\Config\Model\Config\Source\Yesno as BooleanOptions;

class Author extends GenericForm implements TabInterface
{
    /**
     * @var WysiwygConfig
     */
    protected $wysiwygConfig;

    /**
     * @var Country
     */
    protected $countryOptions;

    /**
     * @var Award
     */
    protected $awardOptions;

    /**
     * @var Type
     */
    protected $typeOptions;

    /**
     * @var BooleanOptions
     */
    protected $booleanOptions;

    /**
     * @param WysiwygConfig $wysiwygConfig
     * @param Country $countryOptions
     * @param Award $awardOptions
     * @param Type $typeOptions
     * @param BooleanOptions $booleanOptions
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        WysiwygConfig $wysiwygConfig,
        Country $countryOptions,
        Award $awardOptions,
        Type $typeOptions,
        BooleanOptions $booleanOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    )
    {
        $this->wysiwygConfig    = $wysiwygConfig;
        $this->countryOptions   = $countryOptions;
        $this->awardOptions     = $awardOptions;
        $this->typeOptions      = $typeOptions;
        $this->booleanOptions   = $booleanOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

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
                'legend' => __('Author Information'),
                'class'  => 'fieldset-wide'
            ]
        );

        $fieldset->addType('image', 'Sample\News\Block\Adminhtml\Author\Helper\Image');
        $fieldset->addType('file', 'Sample\News\Block\Adminhtml\Author\Helper\File');

        if ($author->getId()) {
            $fieldset->addField(
                'author_id',
                'hidden',
                ['name' => 'author_id']
            );
        }
        $fieldset->addField(
            'name',
            'text',
            [
                'name'      => 'name',
                'label'     => __('Name'),
                'title'     => __('Name'),
                'required'  => true,
            ]
        );
        $fieldset->addField(
            'url_key',
            'text',
            [
                'name'      => 'url_key',
                'label'     => __('URL Key'),
                'title'     => __('URL Key'),
            ]
        );
        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'hidden',
                [
                    'name'      => 'stores[]',
                    'value'     => $this->_storeManager->getStore(true)->getId()
                ]
            );
            $author->setStoreId($this->_storeManager->getStore(true)->getId());
        }
        $fieldset->addField(
            'is_active',
            'select',
            [
                'label'     => __('Is Active'),
                'title'     => __('Is Active'),
                'name'      => 'is_active',
                'required'  => true,
                'options'   => $author->getAvailableStatuses(),
            ]
        );
        $fieldset->addField(
            'in_rss',
            'select',
            [
                'label'     => __('Show in RSS'),
                'title'     => __('Show in RSS'),
                'name'      => 'in_rss',
                'required'  => true,
                'options'   => $this->booleanOptions->toArray()
            ]
        );
        $fieldset->addField(
            'biography',
            'editor',
            [
                'name'      => 'biography',
                'label'     => __('Biography'),
                'title'     => __('Biography'),
                'style'     => 'height:36em',
                'required'  => true,
                'config'    => $this->wysiwygConfig->getConfig()
            ]
        );
        $fieldset->addField(
            'dob',
            'date',
            [
                'name'        => 'dob',
                'label'       => __('Date of birth'),
                'title'       => __('Date of birth'),
                'image'       => $this->getViewFileUrl('images/grid-cal.png'),
                'date_format' => $this->_localeDate->getDateFormat(
                    \IntlDateFormatter::SHORT
                ),
                'class' => 'validate-date'
            ]
        );
        $fieldset->addField(
            'type',
            'select',
            [
                'label'     => __('Type'),
                'title'     => __('Type'),
                'name'      => 'type',
                'required'  => true,
                'options'   => $this->typeOptions->getOptions()
            ]
        );
        $fieldset->addField(
            'awards',
            'multiselect',
            [
                'name'        => 'awards',
                'label'       => __('Awards'),
                'title'       => __('Awards'),
                'values'      => $this->awardOptions->toOptionArray(),
            ]
        );
        $fieldset->addField(
            'avatar',
            'image',
            [
                'name'        => 'avatar',
                'label'       => __('Avatar'),
                'title'       => __('Avatar'),
            ]
        );
        $fieldset->addField(
            'resume',
            'file',
            [
                'name'        => 'resume',
                'label'       => __('Resume'),
                'title'       => __('Resume'),
            ]
        );
        $fieldset->addField(
            'country',
            'select',
            [
                'name'        => 'country',
                'label'       => __('Country'),
                'title'       => __('Country'),
                'options'     => $this->countryOptions->getOptions()
            ]
        );
        $authorData = $this->_session->getData('sample_news_author_data', true);
        if ($authorData) {
            $author->addData($authorData);
        } else {
            if (!$author->getId()) {
                $author->addData($author->getDefaultValues());
            }
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
        return __('Author');
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
