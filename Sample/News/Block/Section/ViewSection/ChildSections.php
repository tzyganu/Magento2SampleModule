<?php
namespace Sample\News\Block\Section\ViewSection;

class ChildSections extends \Sample\News\Block\Section\ListSection {
    protected $_coreRegistry;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Sample\News\Model\Resource\Section\CollectionFactory $sectionCollectionFactory,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $sectionCollectionFactory, $urlFactory, $data);
    }
    protected function _prepareLayout(){
        $this->getSections()->addFieldToFilter('parent_id', $this->getCurrentSection()->getId());
        return $this;
    }
    public function getCurrentSection(){
        return $this->_coreRegistry->registry('current_section');
    }

}