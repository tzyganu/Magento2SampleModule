<?php
namespace Sample\News\Block\Adminhtml\Author\Grid;

use Magento\Framework\Data\CollectionDataSourceInterface;
use Sample\News\Model\ResourceModel\Author\Collection;
use Magento\Backend\Block\Widget\Grid\Column;

class DataSource implements CollectionDataSourceInterface
{
    /**
     * filter by store
     *
     * @param Collection $collection
     * @param Column $column
     * @return $this
     */
    public function filterStoreCondition(Collection $collection, Column $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $collection->addStoreFilter($value);
        return $this;
    }
}
