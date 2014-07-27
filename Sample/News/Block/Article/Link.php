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
namespace Sample\News\Block\Article;

class Link
    extends \Magento\Framework\View\Element\Html\Link {
    /**
     * @var \Sample\News\Helper\Article
     */
    protected $_articleHelper;

    /**
     * @access public
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
     * @access public
     * @return string
     */
    public function getHref() {
        return $this->_articleHelper->getArticlesUrl();
    }
}
