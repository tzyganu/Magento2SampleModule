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
namespace Sample\News\Block\Section\ViewSection;

class Article
    extends \Magento\Framework\View\Element\Template {
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry;
    /**
     * @var \Magento\Catalog\Model\Resource\Product\Collection
     */
    protected $_articleCollection;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return \Sample\News\Model\Section
     */
    public function getSection(){
        return $this->_coreRegistry->registry('current_section');
    }

    /**
     * @return \Magento\Catalog\Model\Resource\Product\Collection
     */
    public function getArticleCollection() {
        if (is_null($this->_articleCollection)) {
            $collection = $this->getSection()->getSelectedArticlesCollection()
                ->addStoreFilter($this->_storeManager->getStore())
                ->addFieldToFilter('status', 1);
            $collection->getSelect()->order('related_article.position');
            $this->_articleCollection = $collection;
        }
        return $this->_articleCollection;
    }
}
