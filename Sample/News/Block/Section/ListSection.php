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
namespace Sample\News\Block\Section;


class ListSection extends \Magento\Framework\View\Element\Template {
    /**
     * @var \Sample\News\Model\Resource\Section\CollectionFactory
     */
    protected $_sectionCollectionFactory;
    /**
     * @var \Magento\Framework\UrlFactory
     */
    protected $_urlFactory;


    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Sample\News\Model\Resource\Article\CollectionFactory $articleCollectionFactory
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Sample\News\Model\Resource\Section\CollectionFactory $sectionCollectionFactory,
        \Magento\Framework\UrlFactory $urlFactory,
        array $data = []
    ) {
        $this->_sectionCollectionFactory = $sectionCollectionFactory;
        $this->_urlFactory = $urlFactory;
        parent::__construct($context, $data);
    }

    /**
     * @access protected
     */
    protected  function _construct() {
        parent::_construct();
        $sections = $this->_sectionCollectionFactory->create()
            ->addFieldToFilter('status', 1)
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('name','desc');
        $this->setSections($sections);
    }

    /**
     * @return bool
     */
    public function isRssEnabled() {
        return
            $this->_scopeConfig->getValue('rss/config/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) &&
            $this->_scopeConfig->getValue('sample_news/section/rss', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @access protected
     * @return $this
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $this->getSections()->addFieldToFilter('level', 1);
        if ($this->getDisplayMode() == 0){
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'sample_news.article.list.pager')
                ->setCollection($this->getSections());
            $this->setChild('pager', $pager);
            $this->getSections()->load();
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($this->isRssEnabled()) {
            $title = __('Sections RSS Feed');
            $headBlock->addRss($title, $this->getRssLink());
        }
        return $this;
    }

    /**
     * @access public
     * @return string
     */
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    /**
     * @return string
     */
    public function getRssLink() {
        return $this->_urlBuilder->getUrl('sample_news/section/rss', array('store' => $this->_storeManager->getStore()->getId()));
    }
    public function getDisplayMode(){
        return $this->_scopeConfig->getValue('sample_news/section/tree', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function drawSection(\Sample\News\Model\Section $section, $level = 0){
        $html = '';
        $recursion = $this->getRecursion();
        if ($recursion !== '0' && $level >= $recursion){
            return '';
        }
        if (!$section->getStatus()){
            return '';
        }
        $children = $section->getChildrenSections();
        $activeChildren = array();
        if ($recursion == 0 || $level < $recursion-1){
            foreach ($children as $child) {
                if ($child->getStatus()) {
                    $activeChildren[] = $child;
                }
            }
        }
        $html .= '<li>';
        $html .= '<a href="'.$section->getSectionUrl().'">'.$section->getName().'</a>';
        if (count($activeChildren) > 0) {
            $html .= '<ul>';
            foreach ($children as $child){
                $html .= $this->drawSection($child, $level+1);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
        return $html;
    }
    /**
     * get recursion
     * @access public
     * @return int
     * {{qwertyuiop}}
     */
    public function getRecursion(){
        if (!$this->hasData('recursion')){
            $this->setData('recursion', $this->_scopeConfig->getValue('sample_news/section/recursion'));
        }
        return $this->getData('recursion');
    }

}
