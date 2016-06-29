<?php
/**
 * Sample_News extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Sample
 * @package   Sample_News
 * @copyright 2016 Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru
 */
namespace Sample\News\Model\Source;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;
use Magento\Framework\Option\ArrayInterface;

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
     * @return array
     */
    public function getOptions()
    {
        $countryOptions = $this->toOptionArray();
        $_options = [];
        foreach ($countryOptions as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
}
