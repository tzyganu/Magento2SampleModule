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
class Observer {
    protected $_sectionHelper;
    public function __construct(
        \Sample\News\Helper\Section $sectionHelper
    ) {
        $this->_sectionHelper = $sectionHelper;
    }
    public function addLinksToTopMenu($observer) {

        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        //$action = Mage::app()->getFrontController()->getAction()->getFullActionName();
        $action = 'sample_news_section_index';

        $sectionNodeId = 'section';
        $data = array(
            'name' => __('Sections'),
            'id' => $sectionNodeId,
            'url' => $this->_sectionHelper->getSectionsUrl(),
            'is_active' => ($action == 'sample_news_section_index' || $action == 'sample_news_section_index')
        );
        $sectionNode = new \Magento\Framework\Data\Tree\Node($data, 'id', $tree, $menu);
        $menu->addChild($sectionNode);
        return $this;
    }
}