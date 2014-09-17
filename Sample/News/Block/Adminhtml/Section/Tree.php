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

use Sample\News\Model\Resource\Section\Collection;
use Magento\Framework\Data\Tree\Node;

class Tree extends \Sample\News\Block\Adminhtml\Section\AbstractSection
{
    /**
     * @var string
     */
    protected $_template = 'catalog/category/tree.phtml';

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendSession;

    /**
     * @var \Magento\Framework\DB\Helper
     */
    protected $_resourceHelper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Sample\News\Model\Resource\Section\Tree $categoryTree,
        \Magento\Framework\Registry $registry,
        \Sample\News\Model\SectionFactory $sectionFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Backend\Model\Auth\Session $backendSession,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_resourceHelper = $resourceHelper;
        $this->_backendSession = $backendSession;
        parent::__construct($context, $categoryTree, $registry, $sectionFactory, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(0);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $addUrl = $this->getUrl("*/*/add", array('_current' => true, 'id' => null, '_query' => false));

        $this->addChild(
            'add_child_section_button',
            'Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Add Child Section'),
                'onclick' => "addNew('" . $addUrl . "', false)",
                'class' => 'add',
                'id' => 'add_child_section_button',
                'style' => $this->canAddChildSection() ? '' : 'display: none;'
            )
        );

        $this->addChild(
            'add_root_section_button',
            'Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Add Root Section'),
                'onclick' => "addNew('" . $addUrl . "', true)",
                'class' => 'add',
                'id' => 'add_root_section_button'
            )
        );

        return parent::_prepareLayout();
    }

    /**
     * Retrieve list of categories with name containing $namePart and their parents
     *
     * @param string $namePart
     * @return string
     */
    public function getSuggestedSectionsJson($namePart)
    {
        $collection = $this->_sectionFactory->create()->getCollection();

        $matchingNamesCollection = clone $collection;
        $escapedNamePart = $this->_resourceHelper->addLikeEscape(
            $namePart,
            array('position' => 'any')
        );
        $matchingNamesCollection->addFieldToFilter('name',array('like' => $escapedNamePart))
            ->addFieldToFilter('entity_id',array('neq' => \Sample\News\Helper\Section::ROOT_SECTION_ID));

        $shownSectionIds = array();
        foreach ($matchingNamesCollection as $section) {
            foreach (explode('/', $section->getPath()) as $parentId) {
                $shownSectionIds[$parentId] = 1;
            }
        }

        $collection->addFieldToFilter('entity_id', array('in' => array_keys($shownSectionIds)));

        $sectionById = array(
            \Sample\News\Helper\Section::ROOT_SECTION_ID => array(
                'id' => \Sample\News\Helper\Section::ROOT_SECTION_ID,
                'children' => array()
            )
        );
        foreach ($collection as $section) {
            foreach (array($section->getId(), $section->getParentId()) as $sectionId) {
                if (!isset($sectionById[$sectionId])) {
                    $sectionById[$sectionId] = array('id' => $sectionId, 'children' => array());
                }
            }
            $sectionById[$section->getId()]['status'] = $section->getStatus();
            $sectionById[$section->getId()]['label'] = $section->getName();
            $sectionById[$section->getParentId()]['children'][] =& $sectionById[$section->getId()];
        }

        return $this->_jsonEncoder->encode($sectionById[\Sample\News\Helper\Section::ROOT_SECTION_ID]['children']);
    }

    /**
     * @return string
     */
    public function getAddRootButtonHtml()
    {
        return $this->getChildHtml('add_root_section_button');
    }

    /**
     * @return string
     */
    public function getAddSubButtonHtml()
    {
        return $this->getChildHtml('add_child_section_button');
    }

    /**
     * @return string
     */
    public function getExpandButtonHtml()
    {
        return $this->getChildHtml('expand_button');
    }

    /**
     * @return string
     */
    public function getCollapseButtonHtml()
    {
        return $this->getChildHtml('collapse_button');
    }

    /**
     * @param bool|null $expanded
     * @return string
     */
    public function getLoadTreeUrl($expanded = null)
    {
        $params = array('_current' => true, 'id' => null);
        if (is_null($expanded) && $this->_backendSession->getIsSampleNewsSectionTreeWasExpanded() || $expanded == true) {
            $params['expand_all'] = true;
        }
        return $this->getUrl('*/*/sectionsJson', $params);
    }

    /**
     * @return string
     */
    public function getNodesUrl()
    {
        return $this->getUrl('sample_news/section/jsonTree');
    }


    /**
     * @return bool
     */
    public function getIsWasExpanded()
    {
        return $this->_backendSession->getIsSampleNewsSectionTreeWasExpanded();
    }

    /**
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('sample_news/section/move');
    }

    /**
     * @param mixed|null $parenNodeCategory
     * @return array
     */
    public function getTree($parenNodeSection = null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parenNodeSection));
        $tree = isset($rootArray['children']) ? $rootArray['children'] : array();
        return $tree;
    }

    /**
     * @param mixed|null $parenNodeCategory
     * @return string
     */
    public function getTreeJson($parentNodeSection = null)
    {
        $rootArray = $this->_getNodeJson($this->getRoot($parentNodeSection));
        $json = $this->_jsonEncoder->encode(isset($rootArray['children']) ? $rootArray['children'] : array());
        return $json;
    }

    /**
     * Get JSON of array of categories, that are breadcrumbs for specified category path
     *
     * @param string $path
     * @param string $javascriptVarName
     * @return string
     */
    public function getBreadcrumbsJavascript($path, $javascriptVarName)
    {
        if (empty($path)) {
            return '';
        }

        $sections = $this->_sectionTree->loadBreadcrumbsArray($path);
        if (empty($sections)) {
            return '';
        }
        foreach ($sections as $key => $section) {
            $sections[$key] = $this->_getNodeJson($section);
        }
        return '<script type="text/javascript">' . $javascriptVarName . ' = ' . $this->_jsonEncoder->encode($sections) .
            ';' .
            ($this->canAddChildSection() ? '$("add_child_section_button").show();' : '$("add_child_section_button").hide();') .
            '</script>';
    }

    /**
     * Get JSON of a tree node or an associative array
     *
     * @param Node|array $node
     * @param int $level
     * @return string
     */
    protected function _getNodeJson($node, $level = 0)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Node($node, 'entity_id', new \Magento\Framework\Data\Tree());
        }

        $item = array();
        $item['text'] = $this->buildNodeName($node);


        $item['id'] = $node->getId();
        $item['path'] = $node->getData('path');

        $item['cls'] = 'folder ' . ($node->getStatus() ? 'active-category' : 'no-active-category');
        $allowMove = $this->_isSectionMoveable($node);
        $item['allowDrop'] = $allowMove;
        $item['allowDrag'] = true;

        if ((int)$node->getChildrenCount() > 0) {
            $item['children'] = array();
        }

        $isParent = $this->_isParentSelectedSection($node);

        if ($node->hasChildren()) {
            $item['children'] = array();
            if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level + 1);
                }
            }
        }

        if ($isParent || $node->getLevel() < 2) {
            $item['expanded'] = true;
        }

        return $item;
    }

    /**
     * Get category name
     *
     * @param \Magento\Framework\Object $node
     * @return string
     */
    public function buildNodeName($node)
    {
        $result = $this->escapeHtml($node->getName());
        return $result;
    }

    /**
     * @param Node|array $node
     * @return bool
     */
    protected function _isSectionMoveable($node)
    {
        $options = new \Magento\Framework\Object(array('is_moveable' => true, 'section' => $node));

        $this->_eventManager->dispatch('adminhtml_sample_news_section_tree_is_moveable', array('options' => $options));

        return $options->getIsMoveable();
    }

    /**
     * @param Node|array $node
     * @return bool
     */
    protected function _isParentSelectedSection($node)
    {
        if ($node && $this->getSection()) {
            $pathIds = $this->getSection()->getPathIds();
            if (in_array($node->getId(), $pathIds)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if page loaded by outside link to category edit
     *
     * @return boolean
     */
    public function isClearEdit()
    {
        return (bool)$this->getRequest()->getParam('clear');
    }

    /**
     * Check availability of adding root category
     *
     * @return boolean
     */
    public function canAddRootSection()
    {
        $options = new \Magento\Framework\Object(array('is_allow' => true));
        $this->_eventManager->dispatch(
            'adminhtml_sample_news_section_tree_can_add_root_section',
            array('section' => $this->getSection(), 'options' => $options)
        );

        return $options->getIsAllow();
    }

    /**
     * Check availability of adding sub category
     *
     * @return boolean
     */
    public function canAddChildSection()
    {
        $options = new \Magento\Framework\Object(array('is_allow' => true));
        $this->_eventManager->dispatch(
            'adminhtml_sample_news_section_tree_can_add_child_section',
            array('section' => $this->getSection(), 'options' => $options)
        );

        return $options->getIsAllow();
    }
}
