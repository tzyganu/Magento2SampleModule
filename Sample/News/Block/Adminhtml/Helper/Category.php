<?php
namespace Sample\News\Block\Adminhtml\Helper;

use Magento\Framework\Data\Form\Element\Multiselect;
use Magento\Catalog\Model\Resource\Category\CollectionFactory;
use Magento\Backend\Helper\Data as DataHelper;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use Magento\Framework\Data\Form\Element\CollectionFactory as ElementCollectionFactory;
use Magento\Framework\Escaper;

/**
 * @method mixed getValue()
 */

class Category extends Multiselect {
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * Backend data
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendData;

    /**
     * @var \Magento\Catalog\Model\Resource\Category\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;


    /**
     * @param \Magento\Catalog\Model\Resource\Category\CollectionFactory $collectionFactory
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
        CollectionFactory $collectionFactory,
        DataHelper $backendData,
        LayoutInterface $layout,
        EncoderInterface $jsonEncoder,
        AuthorizationInterface $authorization,
        ElementFactory $factoryElement,
        ElementCollectionFactory $factoryCollection,
        Escaper $escaper,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->backendData = $backendData;
        $this->layout = $layout;
        $this->jsonEncoder = $jsonEncoder;
        $this->authorization = $authorization;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);

    }

    /**
     * Get no display
     *
     * @return bool
     */
    public function getNoDisplay()
    {
        $isNotAllowed = !$this->authorization->isAllowed('Magento_Catalog::categories');
        return $this->getData('no_display') || $isNotAllowed;
    }

    /**
     * Get values for select
     * @return array
     */
    public function getValues()
    {
        $collection = $this->_getCategoriesCollection();
        $values = $this->getValue();
        if (!is_array($values)) {
            $values = explode(',', $values);
        }
        $collection->addAttributeToSelect('name');
        $collection->addIdFilter($values);
        $options = [];
        foreach ($collection as $category) {
            /** @var \Magento\Catalog\Model\Category $category */
            $options[] = ['label' => $category->getName(), 'value' => $category->getId()];
        }
        return $options;
    }

    /**
     * Get categories collection
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    protected function _getCategoriesCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * Attach category suggest widget initialization
     * @return string
     */
    public function getAfterElementHtml()
    {
        $htmlId = $this->getHtmlId();
        $suggestPlaceholder = __('start typing to search category');
        $selectorOptions = $this->jsonEncoder->encode($this->_getSelectorOptions());
        $newCategoryCaption = __('New Category');
        /** @var \Magento\Backend\Block\Widget\Button $button */
        $button = $this->layout->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData([
                'id' => 'add_category_button',
                'label' => $newCategoryCaption,
                'title' => $newCategoryCaption,
                'onclick' => 'jQuery("#new-category").dialog("open")',
                'disabled' => $this->getDisabled()
            ]);
        $widgetOptions = $this->jsonEncoder->encode(
            array(
                'suggestOptions' => array(
                    'source' => $this->getUrl('catalog/category/suggestCategories'),
                    'valueField' => '#new_category_parent',
                    'className' => 'category-select',
                    'multiselect' => true,
                    'showAll' => true
                ),
                'saveCategoryUrl' => $this->getUrl('catalog/category/save')
            )
        );
        //TODO: move this somewhere else when magento team decides to move it.
        $return = <<<HTML
        <input id="{$htmlId}-suggest" placeholder="$suggestPlaceholder" />
        <script type="text/javascript">
            require(["jquery","mage/mage"],function($) {  // waiting for dependencies at first
                $(function(){ // waiting for page to load to have '#category_ids-template' available
                    $('#new-category').mage('newCategoryDialog', {$widgetOptions});
                    $('#{$htmlId}-suggest').mage('treeSuggest', {$selectorOptions});
                });
            });
        </script>
HTML;
        return $return . $button->toHtml();
    }

    /**
     * Get selector options
     * @return array
     */
    protected function _getSelectorOptions()
    {
        return array(
            'source' => $this->backendData->getUrl('catalog/category/suggestCategories'),
            'valueField' => '#' . $this->getHtmlId(),
            'className' => 'category-select',
            'multiselect' => true,
            'showAll' => true
        );
    }
}
