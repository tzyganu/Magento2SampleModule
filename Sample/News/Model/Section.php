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

class Section
    extends \Magento\Framework\Model\AbstractModel
    implements \Magento\Framework\Object\IdentityInterface {
    /**
     * path to url prefix
     */
    const XML_URL_PREFIX_PATH = 'sample_news/section/url_prefix';
    /**
     * path to url suffix
     */
    const XML_URL_SUFFIX_PATH = 'sample_news/section/url_suffix';
    /**
     * cache tag
     */
    const CACHE_TAG = 'sample_news_section';

    /**
     * @var string
     */
    protected $_cacheTag = 'sample_news_section';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'sample_news_section';
    /**
     * @var null|Resource\Section\Tree
     */
    protected $_treeModel = null;
    /**
     * @var Resource\Section\TreeFactory
     */
    protected $_sectionTreeFactory;
    /**
     * @var \Sample\News\Helper\Section
     */
    protected  $_sectionHelper;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    /**
     * @var ArticleFactory
     */
    protected $_articleFactory;
    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $_filter;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;
    /**
     * @var
     */
    protected $_productCollection;
    /**
     * @var
     */
    protected $_categoryCollection;
    /**
     * @var
     */
    protected $_articleCollection;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_sectionFactory;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(
        \Sample\News\Helper\Section $sectionHelper,
        \Sample\News\Model\SectionFactory $sectionFactory,
        \Sample\News\Model\Resource\Section\Tree $sectionTreeResource,
        \Sample\News\Model\Resource\Section\TreeFactory $sectionTreeFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Sample\News\Model\ArticleFactory $articleFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filter\FilterManager $filter,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_productFactory = $productFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_articleFactory = $articleFactory;
        $this->_sectionFactory = $sectionFactory;
        $this->_treeModel = $sectionTreeResource;
        $this->_sectionTreeFactory = $sectionTreeFactory;
        $this->_sectionHelper = $sectionHelper;
        $this->_filter = $filter;
        $this->_urlBuilder = $urlBuilder;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection);
    }
    /**
     * @access public
     * @return void
     */
    protected function _construct() {
        $this->_init('Sample\News\Model\Resource\Section');
    }

    /**
     * Get identities
     * @return array
     */
    public function getIdentities() {
        return array(self::CACHE_TAG . '_' . $this->getId());
    }

    /**
     * @return string
     */
    public function getSectionUrl() {
        if ($this->getIdentifier()) {
            $identifier = '';
            if ($prefix = $this->_scopeConfig->getValue(self::XML_URL_PREFIX_PATH)) {
                $identifier .= $prefix.'/';
            }
            $identifier .= $this->getIdentifier();
            if ($suffix = $this->_scopeConfig->getValue(self::XML_URL_SUFFIX_PATH)) {
                $identifier .= '.'.$suffix;
            }
            return $this->_urlBuilder->getUrl('', array('_direct' => $identifier));
        }
        return $this->_urlBuilder->getUrl('sample_news/section/view', array('id' => $this->getId()));
    }

    /**
     * @param $identifier
     * @param $storeId
     * @return mixed
     */
    public function checkIdentifier($identifier, $storeId) {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
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

    /**
     * @param string $attributes
     * @return object
     */
    public function getSelectedProductsCollection($attributes = '*') {
        if (is_null($this->_productCollection)) {
            $collection = $this->_productFactory->create()->getResourceCollection();
            $collection->addAttributeToSelect($attributes);
            $collection->joinField('position',
                'sample_news_section_product',
                'position',
                'product_id=entity_id',
                '{{table}}.section_id='.$this->getId(),
                'inner');
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }

    /**
     * @return array|mixed
     */
    public function getArticlesPosition() {
        if (!$this->getId()) {
            return array();
        }
        $array = $this->getData('articles_position');
        if (is_null($array)) {
            $array = $this->getResource()->getArticlesPosition($this);
            $this->setData('articles_position', $array);
        }
        return $array;
    }

    /**
     * @return \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getSelectedArticlesCollection() {
        if (is_null($this->_articleCollection)) {
            $collection = $this->_articleFactory->create()->getResourceCollection();
            $collection->getSelect()->join(
                array('related_article' => $collection->getTable('sample_news_article_section')),
                'related_article.article_id = main_table.entity_id',
                array('position')
            );
            $collection->getSelect()->where('related_article.section_id = ?', $this->getId());
            $this->_articleCollection = $collection;
        }
        return $this->_articleCollection;
    }

    /**
     * @param string $attributes
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function getSelectedCategoriesCollection($attributes = '*') {
        if (is_null($this->_categoryCollection)) {
            $collection = $this->_categoryFactory->create()->getResourceCollection();
            $collection->addAttributeToSelect($attributes);
            $collection->joinField('position',
                'sample_news_section_category',
                'position',
                'category_id=entity_id',
                '{{table}}.section_id='.$this->getId(),
                'inner');
            $this->_categoryCollection = $collection;
        }
        return $this->_categoryCollection;
    }

    /**
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
     * @return array
     */
    public function getDefaultValues() {
        return array(
            'status' => 1,
        );
    }

    /**
     * format the url key
     * @param $string
     * @return string
     */
    public function formatUrlKey($string) {
        return $this->_filter->translitUrl($string);
    }

    /**
     * @return bool
     */
    public function isDeleteable() {
        return $this->getId() && $this->getId() != \Sample\News\Helper\Section::ROOT_SECTION_ID;
    }

    /**
     * @return array|mixed
     */
    public function getPathIds() {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _beforeDelete() {
        if ($this->getResource()->isForbiddenToDelete($this->getId())) {
            throw new \Magento\Framework\Model\Exception("Can't delete root section.");
        }
        return parent::_beforeDelete();
    }

    /**
     * @param $parentId
     * @param $afterSectionId
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     * @throws \Exception
     */
    public function move($parentId, $afterSectionId) {
        /**
         * Validate new parent section id. (section model is used for backward
         * compatibility in event params)
         */
        $parent = $this->_sectionFactory->create()->load($parentId);

        if (!$parent->getId()) {
            throw new \Magento\Framework\Model\Exception(
                __(
                    'Sorry, but we can\'t move the section because we can\'t find the new parent section you selected.'
                )
            );
        }

        if (!$this->getId()) {
            throw new \Magento\Framework\Model\Exception(
                __('Sorry, but we can\'t move the section because we can\'t find the new section you selected.')
            );
        } elseif ($parent->getId() == $this->getId()) {
            throw new \Magento\Framework\Model\Exception(
                __(
                    'We can\'t perform this section move operation because the parent section matches the child category.'
                )
            );
        }

        /**
         * Setting affected section ids for third party engine index refresh
         */
        $this->setMovedSectionId($this->getId());
        $oldParentId = $this->getParentId();
        $oldParentIds = $this->getParentIds();

        $eventParams = array(
            $this->_eventObject => $this,
            'parent' => $parent,
            'section_id' => $this->getId(),
            'prev_parent_id' => $oldParentId,
            'parent_id' => $parentId
        );

        $this->_getResource()->beginTransaction();
        try {
            $this->_eventManager->dispatch($this->_eventPrefix . '_move_before', $eventParams);
            $this->getResource()->changeParent($this, $parent, $afterSectionId);
            $this->_eventManager->dispatch($this->_eventPrefix . '_move_after', $eventParams);
            $this->_getResource()->commit();

            // Set data for indexer
            $this->setAffectedSectionIds(array($this->getId(), $oldParentId, $parentId));
        } catch (\Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        $this->_eventManager->dispatch('section_move', $eventParams);
        $this->_cacheManager->clean(array(self::CACHE_TAG));

        return $this;
    }

    /**
     * @return mixed
     */
    public function getChildrenSections() {
        return $this->getResource()->getChildrenSections($this);
    }

    /**
     * @return string
     */
    public function getChildren() {
        return implode(',', $this->getResource()->getChildren($this, false));
    }

    /**
     * @return bool
     */
    public function getStatusPath() {
        $parents = $this->getParentSections();
        $rootId = $this->_sectionHelper->getRootSectionId();
        foreach ($parents as $parent){
            if ($parent->getId() == $rootId) {
                continue;
            }
            if (!$parent->getStatus()){
                return false;
            }
        }
        return $this->getStatus();

    }

    /**
     * @return mixed
     */
    public function getParentSections(){
        return $this->getResource()->getParentSections($this);
    }
}
