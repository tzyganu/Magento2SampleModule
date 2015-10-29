<?php
namespace Sample\News\Model\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;

class Country implements ArrayInterface
{
    /**
     * @var \Sample\News\Model\Author
     */
    protected $countryCollectionFactory;
    /**
     * @var array
     */
    protected $options;

    /**
     * constructor
     *
     * @param CountryCollectionFactory $countryCollectionFactory
     */
    public function __construct(CountryCollectionFactory $countryCollectionFactory)
    {
        $this->countryCollectionFactory = $countryCollectionFactory;
    }

    /**
     * get options as key value pair
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (is_null($this->options)) {
            $this->options = $this->countryCollectionFactory->create()->toOptionArray(' ');
        }
        return $this->options;
    }

    /**
     * @param array $options
     * @return array
     */
    public function getOptions(array $options = [])
    {
        $countryOptions = $this->toOptionArray($options);
        $_options = [];
        foreach ($countryOptions as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
}
