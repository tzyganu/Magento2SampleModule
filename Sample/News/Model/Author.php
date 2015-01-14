<?php
namespace Sample\News\Model;

use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\Object\IdentityInterface;
use \Magento\Framework\Filter\FilterManager;
use \Magento\Framework\Model\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Model\Resource\AbstractResource;
use \Magento\Framework\Data\Collection\Db;
use Sample\News\Model\Author\Url;
use Magento\Catalog\Model\Resource\Product\CollectionFactory as ProductCollectionFactory;

/**
 * @method string getName()
 * @method Author setUpdatedAt(\string $date)
 * @method Author setCreatedAt(\string $date)
 * @method Author setUrlKey(\string $urlKey)
 * @method array getStores()
 * @method int getStoreId()
 * @method bool hasStores()
 * @method Author setStoreId(\int $storeId)
 * @method Author setAvatar(\string $avatar)
 * @method string getAvatar()
 * @method Author setResume(\string $resumee)
 * @method string getResume()
 * @method Resource\Author _getResource()
 * @method Resource\Author getResource()
 * @method string getUrlKey()
 * @method int getIsActive()
 * @method string getBiography()
 * @method string getDob()
 * @method string getMetaTitle()
 * @method string getMetaDescription()
 * @method string getMetaKeywords()
 * @method Author setProductsData(array $products)
 * @method Author setIsChangedProductList(\bool $changed)
 * @method array|null getProductsData()
 * @method Author setAffectedProductIds(array $productIds)
 * @method int getPosition()
 */
class Author extends AbstractModel implements IdentityInterface
{
    /**
     * status enabled
     *
     * @var int
     */
    const STATUS_ENABLED = 1;
    /**
     * status disabled
     *
     * @var int
     */
    const STATUS_DISABLED = 0;


    protected $urlModel;
    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'sample_news_author';

    /**
     * cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'sample_news_author';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'sample_news_author';

    /**
     * filter model
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    protected $productCollectionFactory;
    protected $productCollection;

    /**
     * @param FilterManager $filter
     * @param Url $urlModel
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource $resource
     * @param Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        FilterManager $filter,
        Url $urlModel,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        Db $resourceCollection = null,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->filter = $filter;
        $this->urlModel = $urlModel;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Sample\News\Model\Resource\Author');
    }

    /**
     * Check if author url key exists
     * return author id if author exists
     *
     * @param string $urlKey
     * @param int $storeId
     * @return int
     */
    public function checkUrlKey($urlKey, $storeId)
    {
        return $this->_getResource()->checkUrlKey($urlKey, $storeId);
    }

    /**
     * Prepare author's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Yes'),
            self::STATUS_DISABLED => __('No')
        ];
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * get default author values
     * @return array
     */
    public function getDefaultValues()
    {
        return [
            'is_active' => self::STATUS_ENABLED
        ];
    }

    /**
     * sanitize the url key
     *
     * @param $string
     * @return string
     */
    public function formatUrlKey($string)
    {
        return $this->filter->translitUrl($string);
    }

    /**
     * @return mixed
     */
    public function getAuthorUrl()
    {
        return $this->urlModel->getAuthorUrl($this);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->getIsActive();
    }

    /**
     * @return array|mixed
     */
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

    public function getSelectedProductsCollection($attributes = '*')
    {
        if (is_null($this->productCollection)) {
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect($attributes);
//            $collection->joinTable(
//                'sample_news_author_product',
//                'product_id=entity_id',
//                ['position'],
//                null,
//                'left'
//            );
//            $collection->getSelect()->where('sample_news_author_product.author_id = ?', $this->getId());
            $collection->joinField('position',
                'sample_news_author_product',
                'position',
                'product_id=entity_id',
                '{{table}}.author_id='.$this->getId(),
                'inner');
            $this->productCollection = $collection;
        }
        return $this->productCollection;
    }


}
