<?php
namespace Sample\News\Block\Article;

class ViewArticle extends \Magento\Framework\View\Element\Template {
    protected $_coreRegistry = null;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
    public function getCurrentArticle(){
        return $this->_coreRegistry->registry('current_article');
    }
}