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
namespace Sample\News\Model;

class Article
    extends \Magento\Framework\Model\AbstractModel
    implements \Magento\Framework\Object\IdentityInterface {
    /**
     * cache tag
     */
    const CACHE_TAG = 'sample_news_article';

    /**
     * @var string
     */
    protected $_cacheTag = 'sample_news_article';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'sample_news_article';

    protected  $_articleHelper;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;
    public function __construct(
        \Sample\News\Helper\Article $articleHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_productFactory = $productFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_articleHelper = $articleHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection);
    }
    /**
     * @access public
     * @return void
     */
    protected function _construct() {
        $this->_init('Sample\News\Model\Resource\Article');
    }

    /**
     * Get identities
     * @access public
     * @return array
     */
    public function getIdentities() {
        return array(self::CACHE_TAG . '_' . $this->getId());
    }

    /**
     * @access public
     * @param $articleIds
     * @param $attributes
     * @return mixed
     */
    public function updateAttributes($articleIds, $attributes){
        return $this->getResource()->updateAttributes($articleIds, $attributes);
    }

    /**
     * @access public
     * @return string
     */
    public function getArticleUrl() {
        return $this->_articleHelper->getArticleUrl($this);
    }

    /**
     * @access public
     * @param $identifier
     * @param $storeId
     * @return mixed
     */
    public function checkIdentifier($identifier, $storeId) {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * @access public
     * @return array|mixed
     */
    public function getProductsPosition() {
        if (!$this->getId()) {
            return array();
        }
        $array = $this->getData('products_position');
        if (is_null($array)) {
            $array = $this->getResource()->getProductsPosition($this);
            $this->setData('products_position', $array);
        }
        return $array;
    }
    //TODO: cache in member var
    /**
     * @access public
     * @param string $attributes
     * @return object
     */
    public function getSelectedProductsCollection($attributes = '*') {
        $collection = $this->_productFactory->create()->getResourceCollection();
        $collection->addAttributeToSelect($attributes);
        $collection->joinField('position',
            'sample_news_article_product',
            'position',
            'product_id=entity_id',
            '{{table}}.article_id='.$this->getId(),
            'inner');
//        echo $collection->getSelect();exit;
        return $collection;
    }

    /**
     * @param string $attributes
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function getSelectedCategoriesCollection($attributes = '*') {
        $collection = $this->_categoryFactory->create()->getResourceCollection();
        $collection->addAttributeToSelect($attributes);
        $collection->joinField('position',
            'sample_news_article_category',
            'position',
            'category_id=entity_id',
            '{{table}}.article_id='.$this->getId(),
            'inner');
        return $collection;
    }

    /**
     * @access protected
     * @return array
     */
    public function getCategoryIds() {
        if (!$this->hasData('category_ids')) {
            $ids = $this->_getResource()->getCategoryIds($this);
            $this->setData('category_ids', $ids);
        }
        return (array) $this->_getData('category_ids');
    }

    /**
     * @access public
     * @return array
     */
    public function getDefaultValues() {
        return array(
            'status' => 1,
        );
    }
}
