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
namespace Sample\News\Block\Article\Catalog;

class Category
    extends \Magento\Framework\View\Element\Template {
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @access public
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @access public
     * @return \Sample\News\Model\Article
     */
    public function getArticle(){
        return $this->_coreRegistry->registry('current_article');
    }

    /**
     * @access public
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function getCategoryCollection() {
        $collection = $this->getArticle()->getSelectedCategoriesCollection()
            ->setStore($this->_storeManager->getStore())
            ->addAttributeToSelect(array('name', 'url_key', 'url_path'))
            ->addAttributeToFilter('is_active', 1);
        $collection->getSelect()->order('at_position.position');
        return $collection;
    }
}
