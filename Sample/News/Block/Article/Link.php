<?php
namespace Sample\News\Block\Article;

class Link extends \Magento\Framework\View\Element\Html\Link {
    /**
     * @var \Sample\News\Helper\Article
     */
    protected $_articleHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Sample\News\Helper\Article $articleHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Sample\News\Helper\Article $articleHelper,
        array $data = array()
    ) {
        $this->_articleHelper = $articleHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_articleHelper->getArticlesUrl();
    }
}