<?php
namespace Sample\News\Model\Resource\Article\Grid;

class Collection extends \Sample\News\Model\Resource\Article\Collection
{
    protected function _afterLoad()
    {
        $this->walk('afterLoad');
        parent::_afterLoad();
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'store_id') {
            return $this->addStoreFilter($field);
        }
    }
}
