<?php
namespace Sample\News\Model\Author\Source;

use Magento\Framework\Option\ArrayInterface;

class Award implements ArrayInterface
{
    const AWARD_1 = 1;
    const AWARD_2 = 2;
    const AWARD_3 = 3;

    /**
     * get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_options = [
            [
                'value' => self::AWARD_1,
                'label' => __('Award 1')
            ],
            [
                'value' => self::AWARD_2,
                'label' => __('Award 2')
            ],
            [
                'value' => self::AWARD_3,
                'label' => __('Award 3')
            ]
        ];
        return $_options;
    }

    //TODO move this in parent class
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
