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
namespace Sample\News\Test\Unit\Model;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Sample\News\Model\Uploader;
use Sample\News\Model\UploaderPool;

class UploaderPoolTest extends \PHPUnit_Framework_TestCase
{
    protected $objectManager;
    /**
     * @var UploaderPool
     */
    protected $uploaderPool;

    /**
     * setup tests
     */
    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        /** @var \PHPUnit_Framework_MockObject_MockObject|ObjectManagerInterface $objectManagerMock */
        $objectManagerMock = $this->getMockBuilder(ObjectManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|Uploader $uploaderMock */
        $uploaderMock = $this->getMockBuilder(Uploader::class)
            ->disableOriginalConstructor()
            ->getMock();
        $objectManagerMock->expects($this->any())->method('create')->willReturn($uploaderMock);

        $dataObject = new DataObject();

        $this->uploaderPool = new UploaderPool(
            $objectManagerMock,
            [
                'uploader1' => Uploader::class,
                'uploader2' => $uploaderMock,
                'uploader3' => $dataObject
            ]
        );
    }

    /**
     * @test \Sample\News\Model\UploaderPool::getUploader() when uploader is not found
     * @throws \Exception
     */
    public function testGetUploaderNotFound()
    {
        $type = 'test';
        $this->setExpectedException('\Exception', "Uploader not found for type: ".$type);
        $this->uploaderPool->getUploader($type);
    }

    /**
     * @test \Sample\News\Model\UploaderPool::getUploader() when instantiation is needed
     * @throws \Exception
     */
    public function testGetUploaderInstantiationNeeded()
    {
        $type = 'uploader1';
        $this->assertInstanceOf(Uploader::class, $this->uploaderPool->getUploader($type));
    }

    /**
     * @test \Sample\News\Model\UploaderPool::getUploader() when instantiation is not needed
     * @throws \Exception
     */
    public function testGetUploaderInstantiationNotNeeded()
    {
        $type = 'uploader2';
        $this->assertInstanceOf(Uploader::class, $this->uploaderPool->getUploader($type));
    }

    /**
     * @test \Sample\News\Model\UploaderPool::getUploader() with wrong type returned
     * @throws \Exception
     */
    public function testGetUploaderWrongType()
    {
        $type = 'uploader3';
        $this->setExpectedException('\Exception', "Uploader for type {$type} not instance of ".Uploader::class);
        $this->uploaderPool->getUploader($type);
    }
}
