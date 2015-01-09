<?php
namespace Sample\News\Model;

use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\Object\IdentityInterface;
use \Magento\Framework\Filter\FilterManager;
use \Magento\Framework\Model\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Model\Resource\AbstractResource;
use \Magento\Framework\Data\Collection\Db;

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
 * @method Author setResumee(\string $resumee)
 * @method string getResumee()
 * @method Resource\Author _getResource()
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

    /**
     * constructor
     *
     * @param FilterManager $filter
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource $resource
     * @param Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        FilterManager $filter,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        Db $resourceCollection = null,
        array $data = []
    ) {
        $this->filter = $filter;
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
}
