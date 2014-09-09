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
namespace Sample\News\Model\Resource\Article\Grid;

class Collection
    extends \Sample\News\Model\Resource\Article\Collection {
    /**
     * @access public
     * @return $this
     */
    protected function _afterLoad() {
        $this->walk('afterLoad');
        return parent::_afterLoad();
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null) {
        if ($field == 'store_id') {
            return $this->addStoreFilter($field);
        }
        else parent::addFieldToFilter($field, $condition);
    }
}
