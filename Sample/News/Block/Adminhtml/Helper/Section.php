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
namespace Sample\News\Block\Adminhtml\Helper;

use Magento\Catalog\Model\Resource\Category\Collection;
class Section extends \Magento\Framework\Data\Form\Element\Multiselect {
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * Backend data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData;

    /**
     * @var \Sample\News\Model\Resource\Section\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;


    /**
     * @param \Sample\News\Model\Resource\Section\CollectionFactory $collectionFactory
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param array $data
     */
    public function __construct(
        \Sample\News\Model\Resource\Section\CollectionFactory $collectionFactory,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_backendData = $backendData;
        $this->_layout = $layout;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_authorization = $authorization;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);

    }

    /**
     * Get no display
     * @return bool
     */
    public function getNoDisplay() {
        $isNotAllowed = !$this->_authorization->isAllowed('Sample_News::news_section');
        return $this->getData('no_display') || $isNotAllowed;
    }

    /**
     * Get values for select
     * @return array
     */
    public function getValues() {
        $collection = $this->_getSectionsCollection();
        $values = $this->getValue();
        if (!is_array($values)) {
            $values = explode(',', $values);
        }
        $collection->addIdFilter($values);
        $options = [];
        foreach ($collection as $section) {
            $options[] = ['label' => $section->getName(), 'value' => $section->getId()];
        }
        return $options;
    }

    /**
     * @return \Sample\News\Model\Resource\Section\Collection
     */
    protected function _getSectionsCollection() {
        return $this->_collectionFactory->create();
    }

    /**
     * Attach category suggest widget initialization
     * @access public
     * @return string
     */
    public function getAfterElementHtml() {
        $htmlId = $this->getHtmlId();
        $suggestPlaceholder = __('start typing to search section');
        $selectorOptions = $this->_jsonEncoder->encode($this->_getSelectorOptions());
        $newSectionCaption = __('New Section');

        $button = $this->_layout->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData([
                'id' => 'add_section_button',
                'label' => $newSectionCaption,
                'title' => $newSectionCaption,
                'onclick' => 'jQuery("#new-section").dialog("open")',
                'disabled' => $this->getDisabled()
            ]);
        $widgetOptions = $this->_jsonEncoder->encode(
            array(
                'suggestOptions' => array(
                    'source' => $this->getUrl('sample_news/section/suggestSections'),
                    'valueField' => '#new_section_parent',
                    'className' => 'section-select',
                    'multiselect' => true,
                    'showAll' => true
                ),
                'saveSectionUrl' => $this->getUrl('sample_news/section/save')
            )
        );
        //TODO: move this somewhere else when magento team decides to move it.
        $return = <<<HTML
            <input id="{$htmlId}-suggest" placeholder="$suggestPlaceholder" />
            <script type="text/javascript">
                require(["jquery","mage/mage"],function($) {  // waiting for dependencies at first
                    $(function(){ // waiting for page to load to have '#category_ids-template' available
                        $('#new-section').mage('newCategoryDialog', {$widgetOptions});
                        $('#{$htmlId}-suggest').mage('treeSuggest', {$selectorOptions});
                    });
                });
            </script>
HTML;
        return $return . $button->toHtml();
    }

    /**
     * Get selector options
     * @access protected
     */
    protected function _getSelectorOptions() {
        return array(
            'source' => $this->_backendData->getUrl('sample_news/section/suggestSections'),
            'valueField' => '#' . $this->getHtmlId(),
            'className' => 'section-select',
            'multiselect' => true,
            'showAll' => true
        );
    }
}
