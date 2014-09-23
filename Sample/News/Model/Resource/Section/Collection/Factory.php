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
namespace Sample\News\Model\Resource\Section\Collection;

class Factory {
    /**
     * @var \Magento\Framework\ObjectManager
     */
    private $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager) {
        $this->_objectManager = $objectManager;
    }

    /**
     * Return newly created instance of the section collection
     *
     * @return \Sample\News\Model\Resource\Section\Collection
     */
    public function create() {
        return $this->_objectManager->create('Sample\News\Model\Resource\Section\Collection');
    }
}
