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
namespace Sample\News\Test\Unit\Model\Author;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Sample\News\Model\Author\DataProvider;
use Sample\News\Model\ResourceModel\Author\CollectionFactory;

class DataProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DataProvider
     */
    protected $dataProvider;

    /**
     * set up tests
     */
    protected function setUp()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|PoolInterface  $poolMock */
        $poolMock = $this->getMockBuilder(PoolInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|ModifierInterface $modifierMock */
        $modifierMock = $this->getMockBuilder(ModifierInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $modifierMock->method('modifyMeta')->willReturn($this->getDummyMeta());
        $modifierMock->method('modifyData')->willReturn($this->getDummyData());
        $poolMock->method('getModifiersInstances')->willReturn([$modifierMock]);

        /** @var ModifierInterface|CollectionFactory $collectionFactoryMock */
        $collectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataProvider = new DataProvider(
            'dummy',
            'dummy_id',
            'dummy_id',
            $collectionFactoryMock,
            $poolMock,
            [],
            []
        );
    }

    /**
     * @return array
     */
    protected function getDummyMeta()
    {
        return ['dummy_meta_key'=>'dummy_meta_value'];
    }

    /**
     * @return array
     */
    protected function getDummyData()
    {
        return ['dummy_data_key'=>'dummy_data_value'];
    }

    /**
     * tests DataProvider::prepareMeta()
     */
    public function testPrepareMeta()
    {
        $this->assertEquals($this->getDummyMeta(), $this->dataProvider->prepareMeta([]));
    }

    /**
     * tests DataProvider::getData()
     */
    public function testGetData()
    {
        $this->assertEquals($this->getDummyData(), $this->dataProvider->getData());
    }

}
