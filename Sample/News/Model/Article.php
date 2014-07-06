<?php

namespace Sample\News\Model;

class Article extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\Object\IdentityInterface {
    /**
     * CMS block cache tag
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
    protected $_productFactory;
    public function __construct(
        \Sample\News\Helper\Article $articleHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_productFactory = $productFactory;
        $this->_articleHelper = $articleHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection);
    }
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Sample\News\Model\Resource\Article');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return array(self::CACHE_TAG . '_' . $this->getId());
    }
    public function updateAttributes($articleIds, $attributes){
        return $this->getResource()->updateAttributes($articleIds, $attributes);
    }
    public function getArticleUrl() {
        return $this->_articleHelper->getArticleUrl($this);
    }
    public function checkIdentifier($identifier, $storeId) {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }
    public function getProductsPosition()
    {
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
    public function getSelectedProductsCollection($attributes = '*') {
        $collection = $this->_productFactory->create()->getCollection();
        $collection->addAttributeToSelect($attributes);
        $collection->joinField('position',
            'sample_news_article_product',
            'position',
            'product_id=entity_id',
            '{{table}}.article_id='.$this->getId(),
            'left');
//        echo $collection->getSelect();exit;
        return $collection;
    }

    public function getCategoryIds()
    {
        if (!$this->hasData('category_ids')) {
            $ids = $this->_getResource()->getCategoryIds($this);
            $this->setData('category_ids', $ids);
        }
        return (array) $this->_getData('category_ids');
    }
}