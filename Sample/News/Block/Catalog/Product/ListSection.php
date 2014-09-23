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
namespace Sample\News\Block\Catalog\Product;

class ListSection
    extends \Magento\Framework\View\Element\Template {
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;
    /**
     * @var \Sample\News\Helper\Product
     */
    protected $_productHelper;

    /**
     * @param \Sample\News\Helper\Product $productHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Sample\News\Helper\Product $productHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->_productHelper = $productHelper;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return \Sample\News\Model\Resource\Article\Collection
     */
    public function getSectionCollection() {
        $collection = $this->_productHelper->getSelectedSectionsCollection($this->getProduct());
        $collection->addStoreFilter($this->_storeManager->getStore()->getId());
        $collection->addFieldToFilter('status', 1);
        $collection->getSelect()->order('related_product.position');
        return $collection;
    }

    /**
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct() {
        return $this->_registry->registry('current_product');
    }
}
