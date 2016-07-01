<?php
/**
 * Sample_News extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Sample
 * @package   Sample_News
 * @copyright 2016 Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru
 */
namespace Sample\News\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\Collection\Db;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Sample\News\Api\Data\AuthorInterface;
use Sample\News\Model\Author\Url;
use Sample\News\Model\ResourceModel\Author as AuthorResourceModel;
use Sample\News\Model\Routing\RoutableInterface;
use Sample\News\Model\Source\AbstractSource;


/**
 * @method AuthorResourceModel _getResource()
 * @method AuthorResourceModel getResource()
 */
class Author extends AbstractModel implements AuthorInterface, RoutableInterface
{
    /**
     * @var int
     */
    const STATUS_ENABLED = 1;
    /**
     * @var int
     */
    const STATUS_DISABLED = 0;
    /**
     * @var Url
     */
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

    /**
     * @var UploaderPool
     */
    protected $uploaderPool;

    /**
     * @var \Sample\News\Model\Output
     */
    protected $outputProcessor;

    /**
     * @var AbstractSource[]
     */
    protected $optionProviders;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Output $outputProcessor
     * @param UploaderPool $uploaderPool
     * @param FilterManager $filter
     * @param Url $urlModel
     * @param array $optionProviders
     * @param array $data
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Output $outputProcessor,
        UploaderPool $uploaderPool,
        FilterManager $filter,
        Url $urlModel,
        array $optionProviders = [],
        array $data = [],
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null
    )
    {
        $this->outputProcessor = $outputProcessor;
        $this->uploaderPool    = $uploaderPool;
        $this->filter          = $filter;
        $this->urlModel        = $urlModel;
        $this->optionProviders = $optionProviders;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(AuthorResourceModel::class);
    }

    /**
     * Get in rss
     *
     * @return bool|int
     */
    public function getInRss()
    {
        return $this->getData(AuthorInterface::IN_RSS);
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->getData(AuthorInterface::TYPE);
    }

    /**
     * Get awards
     *
     * @return string
     */
    public function getAwards()
    {
        return $this->getData(AuthorInterface::AWARDS);
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->getData(AuthorInterface::COUNTRY);
    }

    /**
     * set name
     *
     * @param $name
     * @return AuthorInterface
     */
    public function setName($name)
    {
        return $this->setData(AuthorInterface::NAME, $name);
    }

    /**
     * Set in rss
     *
     * @param $inRss
     * @return AuthorInterface
     */
    public function setInRss($inRss)
    {
        return $this->setData(AuthorInterface::IN_RSS, $inRss);
    }

    /**
     * Set biography
     *
     * @param $biography
     * @return AuthorInterface
     */
    public function setBiography($biography)
    {
        return $this->setData(AuthorInterface::BIOGRAPHY, $biography);
    }

    /**
     * Set DOB
     *
     * @param $dob
     * @return AuthorInterface
     */
    public function setDob($dob)
    {
        return $this->setData(AuthorInterface::DOB, $dob);
    }

    /**
     * set type
     *
     * @param $type
     * @return AuthorInterface
     */
    public function setType($type)
    {
        return $this->setData(AuthorInterface::TYPE, $type);
    }

    /**
     * set awards
     *
     * @param $awards
     * @return AuthorInterface
     */
    public function setAwards($awards)
    {
        return $this->setData(AuthorInterface::AWARDS, $awards);
    }

    /**
     * Set country
     *
     * @param $country
     * @return AuthorInterface
     */
    public function setCountry($country)
    {
        return $this->setData(AuthorInterface::COUNTRY, $country);
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData(AuthorInterface::NAME);
    }

    /**
     * Get url key
     *
     * @return string
     */
    public function getUrlKey()
    {
        return $this->getData(AuthorInterface::URL_KEY);
    }

    /**
     * Get is active
     *
     * @return bool|int
     */
    public function getIsActive()
    {
        return $this->getData(AuthorInterface::IS_ACTIVE);
    }

    /**
     * Get biography
     *
     * @return string
     */
    public function getBiography()
    {
        return $this->getData(AuthorInterface::BIOGRAPHY);
    }

    /**
     * @return mixed
     */
    public function getProcessedBiography()
    {
        return $this->outputProcessor->filterOutput($this->getBiography());
    }

    /**
     * Get DOB
     *
     * @return string
     */
    public function getDob()
    {
        return $this->getData(AuthorInterface::DOB);
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->getData(AuthorInterface::AVATAR);
    }

    /**
     * @return bool|string
     * @throws LocalizedException
     */
    public function getAvatarUrl()
    {
        $url = false;
        $avatar = $this->getAvatar();
        if ($avatar) {
            if (is_string($avatar)) {
                $uploader = $this->uploaderPool->getUploader('image');
                $url = $uploader->getBaseUrl().$uploader->getBasePath().$avatar;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the avatar url.')
                );
            }
        }
        return $url;
    }

    /**
     * @return bool|string
     * @throws LocalizedException
     */
    public function getResumeUrl()
    {
        $url = false;
        $resume = $this->getResume();
        if ($resume) {
            if (is_string($resume)) {
                $uploader = $this->uploaderPool->getUploader('file');
                $url = $uploader->getBaseUrl().$uploader->getBasePath().$resume;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the resume url.')
                );
            }
        }
        return $url;
    }

    /**
     * Get resume
     *
     * @return string
     */
    public function getResume()
    {
        return $this->getData(AuthorInterface::RESUME);
    }

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(AuthorInterface::CREATED_AT);
    }

    /**
     * Get updated at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(AuthorInterface::UPDATED_AT);
    }

    /**
     * set url key
     *
     * @param $urlKey
     * @return AuthorInterface
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData(AuthorInterface::URL_KEY, $urlKey);
    }

    /**
     * Set is active
     *
     * @param $isActive
     * @return AuthorInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(AuthorInterface::IS_ACTIVE, $isActive);
    }

    /**
     * set avatar
     *
     * @param $avatar
     * @return AuthorInterface
     */
    public function setAvatar($avatar)
    {
        return $this->setData(AuthorInterface::AVATAR, $avatar);
    }

    /**
     * set resume
     *
     * @param $resume
     * @return AuthorInterface
     */
    public function setResume($resume)
    {
        return $this->setData(AuthorInterface::RESUME, $resume);
    }

    /**
     * set created at
     *
     * @param $createdAt
     * @return AuthorInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(AuthorInterface::CREATED_AT, $createdAt);
    }

    /**
     * set updated at
     *
     * @param $updatedAt
     * @return AuthorInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(AuthorInterface::UPDATED_AT, $updatedAt);
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
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param $storeId
     * @return AuthorInterface
     */
    public function setStoreId($storeId)
    {
        $this->setData(AuthorInterface::STORE_ID, $storeId);
        return $this;
    }

    /**
     * @return array
     */
    public function getStoreId()
    {
        return $this->getData(AuthorInterface::STORE_ID);
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->getData(AuthorInterface::META_TITLE);
    }

    /**
     * @param $metaTitle
     * @return AuthorInterface
     */
    public function setMetaTitle($metaTitle)
    {
        $this->setData(AuthorInterface::META_TITLE, $metaTitle);
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->getData(AuthorInterface::META_DESCRIPTION);
    }

    /**
     * @param $metaDescription
     * @return AuthorInterface
     */
    public function setMetaDescription($metaDescription)
    {
        $this->setData(AuthorInterface::META_DESCRIPTION, $metaDescription);
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->getData(AuthorInterface::META_KEYWORDS);
    }

    /**
     * @param $metaKeywords
     * @return AuthorInterface
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->setData(AuthorInterface::META_KEYWORDS, $metaKeywords);
        return $this;
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
     * @param $attribute
     * @return string
     */
    public function getAttributeText($attribute)
    {
        if (!isset($this->optionProviders[$attribute])) {
            return '';
        }
        if (!($this->optionProviders[$attribute] instanceof AbstractSource)) {
            return '';
        }
        return $this->optionProviders[$attribute]->getOptionText($this->getData($attribute));
    }
}
