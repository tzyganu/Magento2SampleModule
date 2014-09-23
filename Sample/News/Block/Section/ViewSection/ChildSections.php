<?php
namespace Sample\News\Block\Section\ViewSection;

class ChildSections extends \Sample\News\Block\Section\ListSection {
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Sample\News\Model\Resource\Section\CollectionFactory $sectionCollectionFactory
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Element\Template\Context $context,
        \Sample\News\Model\Resource\Section\CollectionFactory $sectionCollectionFactory,
        \Magento\Framework\UrlFactory $urlFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($sectionCollectionFactory, $urlFactory, $context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout(){
        $this->getSections()->addFieldToFilter('parent_id', $this->getCurrentSection()->getId());
        return $this;
    }

    /**
     * @return \Sample\News\Model\Section
     */
    public function getCurrentSection(){
        return $this->_coreRegistry->registry('current_section');
    }

}