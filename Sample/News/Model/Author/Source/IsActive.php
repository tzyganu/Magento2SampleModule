<?php
namespace Sample\News\Model\Author\Source;

use \Magento\Framework\Option\ArrayInterface;
use \Sample\News\Model\Author;

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

    //TODO move this in parent class
    /**
     * get options as key value pair
     *
     * @param array $options
     * @return array
     */
    public function getOptions(array $options = [])
    {
        $_tmpOptions = $this->toOptionArray($options);
        $_options = [];
        foreach ($_tmpOptions as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
}
