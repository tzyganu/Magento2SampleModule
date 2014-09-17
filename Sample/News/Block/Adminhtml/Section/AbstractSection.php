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
namespace Sample\News\Block\Adminhtml\Section;

use Magento\Store\Model\Store;
use Magento\Framework\Data\Tree\Node;

class AbstractSection extends \Magento\Backend\Block\Template {
    protected $_coreRegistry = null;
    protected $_sectionTree;
    protected $_sectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Sample\News\Model\Resource\Section\Tree $sectionTree,
        \Magento\Framework\Registry $registry,
        \Sample\News\Model\SectionFactory $sectionFactory,
        array $data = array()
    ) {
        $this->_sectionTree = $sectionTree;
        $this->_coreRegistry = $registry;
        $this->_sectionFactory = $sectionFactory;
        parent::__construct($context, $data);
    }

    public function getSection() {
        return $this->_coreRegistry->registry('sample_news_section');
    }

    /**
     * @return int|string|null
     */
    public function getSectionId()
    {
        if ($this->getSection()) {
            return $this->getSection()->getId();
        }
        return \Sample\News\Helper\Section::ROOT_SECTION_ID;
    }

    /**
     * @return string
     */
    public function getSectionName()
    {
        return $this->getSection()->getName();
    }

    /**
     * @return mixed
     */
    public function getSectionPath()
    {
        if ($this->getSection()) {
            return $this->getSection()->getPath();
        }
        return \Sample\News\Helper\Section::ROOT_SECTION_ID;
    }



    /**
     * @param mixed|null $parentNodeSection
     * @param int $recursionLevel
     * @return Node|array|null
     */
    public function getRoot($parentNodeSection = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeSection) && $parentNodeSection->getId()) {
            return $this->getNode($parentNodeSection, $recursionLevel);
        }
        $root = $this->_coreRegistry->registry('root');
        if (is_null($root)) {
            $rootId = \Sample\News\Helper\Section::ROOT_SECTION_ID;

            $tree = $this->_sectionTree->load(null, $recursionLevel);

            if ($this->getSection()) {
                $tree->loadEnsuredNodes($this->getSection(), $tree->getNodeById($rootId));
            }

            $tree->addCollectionData($this->getSectionCollection());

            $root = $tree->getNodeById($rootId);

            if ($root && $rootId != \Sample\News\Helper\Section::ROOT_SECTION_ID) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == \Sample\News\Helper\Section::ROOT_SECTION_ID) {
                $root->setName(__('Root'));
            }
            $this->_coreRegistry->register('root', $root);
        }

        return $root;
    }
    /**
     * @return \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
     */
    public function getSectionCollection()
    {
        $collection = $this->getData('section_collection');
        if (is_null($collection)) {
            $collection = $this->_sectionFactory->create()->getCollection();
            $this->setData('section_collection', $collection);
        }
        return $collection;
    }

    /**
     * Get and register categories root by specified categories IDs
     *
     * IDs can be arbitrary set of any categories ids.
     * Tree with minimal required nodes (all parents and neighbours) will be built.
     * If ids are empty, default tree with depth = 2 will be returned.
     *
     * @param array $ids
     * @return mixed
     */
    public function getRootByIds($ids)
    {
        $root = $this->_coreRegistry->registry('root');
        if (null === $root) {
            $ids = $this->_sectionTree->getExistingSectionIdsBySpecifiedIds($ids);
            $tree = $this->_sectionTree->loadByIds($ids);
            $rootId = \Sample\News\Helper\Section::ROOT_SECTION_ID;
            $root = $tree->getNodeById($rootId);
            if ($root && $rootId != \Sample\News\Helper\Section::ROOT_SECTION_ID) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == \Sample\News\Helper\Section::ROOT_SECTION_ID) {
                $root->setName(__('Root'));
            }

            $tree->addCollectionData($this->getSectionCollection());
            $this->_coreRegistry->register('root', $root);
        }
        return $root;
    }

    /**
     * @param mixed $parentNodeCategory
     * @param int $recursionLevel
     * @return Node
     */
    public function getNode($parentNodeSection, $recursionLevel = 2)
    {
        $nodeId = $parentNodeSection->getId();
        $parentId = $parentNodeSection->getParentId();

        $node = $this->_sectionTree->loadNode($nodeId);
        $node->loadChildren($recursionLevel);

        if ($node && $nodeId != \Sample\News\Helper\Section::ROOT_SECTION_ID) {
            $node->setIsVisible(true);
        } elseif ($node && $node->getId() == \Sample\News\Helper\Section::ROOT_SECTION_ID) {
            $node->setName(__('Root'));
        }

        $this->_sectionTree->addCollectionData($this->getSectionCollection());

        return $node;
    }

    /**
     * @param array $args
     * @return string
     */
    public function getSaveUrl(array $args = array())
    {
        $params = array('_current' => true);
        $params = array_merge($params, $args);
        return $this->getUrl('sample_news/*/save', $params);
    }

    /**
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl(
            'sample_news/section/edit',
            array('_current' => true, '_query' => false, 'id' => null, 'parent' => null)
        );
    }
}
