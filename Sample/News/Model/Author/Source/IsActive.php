<?php
namespace Sample\News\Model\Author\Source;

use Magento\Framework\Option\ArrayInterface;
use Sample\News\Model\Author;

class IsActive implements ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Author::STATUS_ENABLED,
                'label' => __('Yes')
            ],[
                'value' => Author::STATUS_DISABLED,
                'label' => __('No')
            ],
        ];
    }

    /**
     * get options as key value pair
     *
     * @return array
     */
    public function getOptions()
    {
        $_tmpOptions = $this->toOptionArray();
        $_options = [];
        foreach ($_tmpOptions as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
}
