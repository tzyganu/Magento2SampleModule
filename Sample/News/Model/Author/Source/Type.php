<?php
namespace Sample\News\Model\Author\Source;

use Magento\Framework\Option\ArrayInterface;

class Type implements ArrayInterface
{
    const COLLABORATOR = 1;
    const EMPLOYEE = 2;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_options = [
            [
                'value' => '',
                'label' => ''
            ],
            [
                'value' => self::COLLABORATOR,
                'label' => __('Collaborator')
            ],
            [
                'value' => self::EMPLOYEE,
                'label' => __('Employee')
            ],
        ];
        return $_options;
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
