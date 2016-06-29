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
namespace Sample\News\Test\Unit\Model\Source;

use Magento\Directory\Model\Country as CountryModel;
use Magento\Directory\Model\ResourceModel\Country\Collection;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Sample\News\Model\Source\Country;

class CountryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CollectionFactory
     */
    protected $countryCollectionFactoryMock;
    /**
     * @var Country
     */
    protected $countryList;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Collection
     */
    protected $countryCollection;

    /**
     * setup tests
     */
    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->countryCollectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $countryMock = $this->getMockBuilder(CountryModel::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->countryCollection = $this->objectManager->getCollectionMock(Collection::class, [$countryMock]);
        $this->countryCollectionFactoryMock->method('create')->willReturn($this->countryCollection);

        $this->countryList = new Country($this->countryCollectionFactoryMock);
    }

    /**
     * @test \Sample\News\Model\Source\Country::toOptionArray result is memoized
     */
    public function testMemoizedOptionArray()
    {
        $this->countryCollection->method('toOptionArray')->willReturn(['baz' => 'qux']);
        $this->countryCollection->expects($this->once())->method('toOptionArray');
        $result1 = $this->countryList->toOptionArray();
        $result2 = $this->countryList->toOptionArray();
        $this->assertSame($result1, $result2);
    }
}
