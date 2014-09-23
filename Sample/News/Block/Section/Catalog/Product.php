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
namespace Sample\News\Block\Section\Catalog;

class Product
    extends \Magento\Framework\View\Element\Template {
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;
    /**
     * @var \Magento\Catalog\Model\Product\Visibility|null
     */
    protected $_productVisibility = null;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_productVisibility = $productVisibility;
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
    public function getProductCollection() {
        $collection = $this->getSection()->getSelectedProductsCollection()
            ->setStore($this->_storeManager->getStore())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->setVisibility($this->_productVisibility->getVisibleInCatalogIds());
        $collection->getSelect()->order('position');
        return $collection;
    }
}
