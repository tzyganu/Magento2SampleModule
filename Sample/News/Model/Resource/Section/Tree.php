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
namespace Sample\News\Model\Resource\Section;
class Tree extends \Magento\Framework\Data\Tree\Dbp {
    const ID_FIELD = 'id';

    const PATH_FIELD = 'path';

    const ORDER_FIELD = 'order';

    const LEVEL_FIELD = 'level';

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $_eventManager;

    private $_collectionFactory;

    /**
     * Categories resource collection
     *
     * @var Collection
     */
    protected $_collection;


    /**
     * Inactive categories ids
     *
     * @var array
     */
    protected $_inactiveSectionIds = null;

    /**
     * Store id
     *
     * @var integer
     */
    protected $_storeId = null;

    /**
     * @var \Magento\Framework\App\Resource
     */
    protected $_coreResource;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Cache
     *
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $_cache;

    /**
     * Catalog category
     *
     * @var \Magento\Catalog\Model\Resource\Category
     */
    protected $_newsSection;
    protected $_inactiveItems;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Model\Resource\Category $catalogCategory
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Catalog\Model\Attribute\Config $attributeConfig
     * @param \Magento\Catalog\Model\Resource\Category\Collection\Factory $collectionFactory
     */
    public function __construct(
        \Sample\News\Model\Resource\Section $newsSection,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Sample\News\Model\Resource\Section\Collection\Factory $collectionFactory
    ) {
        $this->_newsSection = $newsSection;
        $this->_cache = $cache;
        $this->_storeManager = $storeManager;
        $this->_coreResource = $resource;
        parent::__construct(
            //TODO: check connection parameter
            $resource->getConnection('catalog_write'),
            $resource->getTableName('sample_news_section'),
            array(
                \Magento\Framework\Data\Tree\Dbp::ID_FIELD => 'entity_id',
                \Magento\Framework\Data\Tree\Dbp::PATH_FIELD => 'path',
                \Magento\Framework\Data\Tree\Dbp::ORDER_FIELD => 'position',
                \Magento\Framework\Data\Tree\Dbp::LEVEL_FIELD => 'level'
            )
        );
        $this->_eventManager = $eventManager;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return store id
     *
     * @return integer
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            $this->_storeId = $this->_storeManager->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Enter description here...
     *
     * @param Collection $collection
     * @param boolean $sorted
     * @param array $exclude
     * @param boolean $toLoad
     * @param boolean $onlyActive
     * @return $this
     */
    public function addCollectionData(
        $collection = null,
        $sorted = false,
        $exclude = array(),
        $toLoad = true,
        $onlyActive = false
    ) {
        if (is_null($collection)) {
            $collection = $this->getCollection($sorted);
        } else {
            $this->setCollection($collection);
        }

        if (!is_array($exclude)) {
            $exclude = array($exclude);
        }

        $nodeIds = array();
        foreach ($this->getNodes() as $node) {
            if (!in_array($node->getId(), $exclude)) {
                $nodeIds[] = $node->getId();
            }
        }
        $collection->addIdFilter($nodeIds);
        if ($onlyActive) {

            $disabledIds = $this->_getDisabledIds($collection);
            if ($disabledIds) {
                $collection->addFieldToFilter('entity_id', array('nin' => $disabledIds));
            }
            $collection->addFieldToFilter('status', 1);
        }

        if ($toLoad) {
            $collection->load();

            foreach ($collection as $section) {
                if ($this->getNodeById($section->getId())) {
                    $this->getNodeById($section->getId())->addData($section->getData());
                }
            }

            foreach ($this->getNodes() as $node) {
                if (!$collection->getItemById($node->getId()) && $node->getParent()) {
                    $this->removeNode($node);
                }
            }
        }

        return $this;
    }

    /**
     * Add inactive categories ids
     *
     * @param mixed $ids
     * @return $this
     */
    public function addInactiveSectionIds($ids)
    {
        if (!is_array($this->_inactiveSectionIds)) {
            $this->_initInactiveSectionIds();
        }
        $this->_inactiveSectionIds = array_merge($ids, $this->_inactiveSectionIds);
        return $this;
    }

    /**
     * Retrieve inactive section ids
     *
     * @return $this
     */
    protected function _initInactiveSectionIds()
    {
        $this->_inactiveSectionIds = array();
        $this->_eventManager->dispatch('sample_news_tree_init_inactive_section_ids', array('tree' => $this));
        return $this;
    }

    /**
     * Retrieve inactive categories ids
     *
     * @return array
     */
    public function getInactiveSectionIds()
    {
        if (!is_array($this->_inactiveSectionIds)) {
            $this->_initInactiveSectionIds();
        }

        return $this->_inactiveSectionIds;
    }


    /**
     * Check is category items active
     *
     * @param int $id
     * @return boolean
     */
    protected function _getItemIsActive($id)
    {
        if (!in_array($id, $this->_inactiveSectionIds)) {
            return true;
        }
        return false;
    }

    /**
     * Get categories collection
     *
     * @param boolean $sorted
     * @return Collection
     */
    public function getCollection($sorted = false)
    {
        if (is_null($this->_collection)) {
            $this->_collection = $this->_getDefaultCollection($sorted);
        }
        return $this->_collection;
    }

    /**
     * Clean unneeded collection
     *
     * @param Collection|array $object
     * @return void
     */
    protected function _clean($object)
    {
        if (is_array($object)) {
            foreach ($object as $obj) {
                $this->_clean($obj);
            }
        }
        unset($object);
    }

    /**
     * Enter description here...
     *
     * @param Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        if (!is_null($this->_collection)) {
            $this->_clean($this->_collection);
        }
        $this->_collection = $collection;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param boolean $sorted
     * @return Collection
     */
    protected function _getDefaultCollection($sorted = false)
    {
        $collection = $this->_collectionFactory->create();

        if ($sorted) {
            if (is_string($sorted)) {
                // $sorted is supposed to be attribute name
                $collection->addFieldToSort($sorted);
            } else {
                $collection->addFieldToSort('name');
            }
        }

        return $collection;
    }

    /**
     * Executing parents move method and cleaning cache after it
     *
     * @param mixed $category
     * @param mixed $newParent
     * @param mixed $prevNode
     * @return void
     */
    public function move($section, $newParent, $prevNode = null)
    {
        $this->_newsSection->move($section->getId(), $newParent->getId());
        parent::move($section, $newParent, $prevNode);

        $this->_afterMove();
    }

    /**
     * Move tree after
     *
     * @return $this
     */
    protected function _afterMove()
    {
        $this->_cache->clean(array(\Sample\News\Model\Section::CACHE_TAG));
        return $this;
    }

    /**
     * Load whole category tree, that will include specified categories ids.
     *
     * @param array $ids
     * @param bool $addCollectionData
     * @return $this|bool
     */
    public function loadByIds($ids, $addCollectionData = true)
    {
        $levelField = $this->_conn->quoteIdentifier('level');
        $pathField = $this->_conn->quoteIdentifier('path');
        // load first two levels, if no ids specified
        if (empty($ids)) {
            $select = $this->_conn->select()->from($this->_table, 'entity_id')->where($levelField . ' <= 1');
            $ids = $this->_conn->fetchCol($select);
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        foreach ($ids as $key => $id) {
            $ids[$key] = (int)$id;
        }

        // collect paths of specified IDs and prepare to collect all their parents and neighbours
        $select = $this->_conn->select()->from($this->_table, array('path', 'level'))->where('entity_id IN (?)', $ids);
        $where = array($levelField . '=0' => true);

        foreach ($this->_conn->fetchAll($select) as $item) {
            $pathIds = explode('/', $item['path']);
            $level = (int)$item['level'];
            while ($level > 0) {
                $pathIds[count($pathIds) - 1] = '%';
                $path = implode('/', $pathIds);
                $where["{$levelField}={$level} AND {$pathField} LIKE '{$path}'"] = true;
                array_pop($pathIds);
                $level--;
            }
        }
        $where = array_keys($where);

        // get all required records
        if ($addCollectionData) {
            $select = $this->_createCollectionDataSelect();
        } else {
            $select = clone $this->_select;
            $select->order($this->_orderField . ' ' . \Magento\Framework\DB\Select::SQL_ASC);
        }
        $select->where(implode(' OR ', $where));

        // get array of records and add them as nodes to the tree
        $arrNodes = $this->_conn->fetchAll($select);
        if (!$arrNodes) {
            return false;
        }
        $childrenItems = array();
        foreach ($arrNodes as $key => $nodeInfo) {
            $pathToParent = explode('/', $nodeInfo[$this->_pathField]);
            array_pop($pathToParent);
            $pathToParent = implode('/', $pathToParent);
            $childrenItems[$pathToParent][] = $nodeInfo;
        }
        $this->addChildNodes($childrenItems, '', null);
        return $this;
    }

    /**
     * Load array of category parents
     *
     * @param string $path
     * @param bool $addCollectionData
     * @param bool $withRootNode
     * @return array
     */
    public function loadBreadcrumbsArray($path, $addCollectionData = true, $withRootNode = false)
    {
        $pathIds = explode('/', $path);
        if (!$withRootNode) {
            array_shift($pathIds);
        }
        $result = array();
        if (!empty($pathIds)) {
            if ($addCollectionData) {
                $select = $this->_createCollectionDataSelect(false);
            } else {
                $select = clone $this->_select;
            }
            $select->where(
                'main_table.entity_id IN(?)',
                $pathIds
            )->order(
                    $this->_conn->getLengthSql('main_table.path') . ' ' . \Magento\Framework\DB\Select::SQL_ASC
                );
            $result = $this->_conn->fetchAll($select);
        }
        return $result;
    }



    protected function _createCollectionDataSelect($sorted = true)
    {
        $select = $this->_getDefaultCollection($sorted ? $this->_orderField : false)->getSelect();
        return $select;
    }

    /**
     * Get real existing category ids by specified ids
     *
     * @param array $ids
     * @return array
     */
    public function getExistingSectionIdsBySpecifiedIds($ids)
    {
        if (empty($ids)) {
            return array();
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $select = $this->_conn->select()->from($this->_table, array('entity_id'))->where('entity_id IN (?)', $ids);
        return $this->_conn->fetchCol($select);
    }
}
