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


use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;
use Sample\News\Model\Uploader as UploaderModel;

class UploaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     *
     * dummy base path
     */
    const BASE_PATH = 'base/path';

    /**
     * @var string
     *
     * dummy base tmp path
     */
    const BASE_TMP_PATH = 'base/tmp/path';

    /**
     * @var \Sample\News\Model\Uploader
     */
    protected $uploader;

    /**
     * setup tests
     */
    protected function setUp()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Database $coreFileStorageDatabaseMock */
        $coreFileStorageDatabaseMock = $this->getMockBuilder(Database::class)
            ->disableOriginalConstructor()
            ->getMock();
        $coreFileStorageDatabaseMock->method('copyFile')->willReturn($coreFileStorageDatabaseMock);
        /** @var \PHPUnit_Framework_MockObject_MockObject|Filesystem $fileSystemMock */
        $fileSystemMock = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|WriteInterface $writeInterfaceMock */
        $writeInterfaceMock = $this->getMockBuilder(WriteInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $writeInterfaceMock->method('renameFile')->willReturn($writeInterfaceMock);
        $fileSystemMock->method('getDirectoryWrite')->willReturn($writeInterfaceMock);
        /** @var \PHPUnit_Framework_MockObject_MockObject|UploaderFactory $uploaderFactoryMock */
        $uploaderFactoryMock = $this->getMockBuilder(UploaderFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|Uploader $uploaderMock */
        $uploaderMock = $this->getMockBuilder(Uploader::class)
            ->disableOriginalConstructor()
            ->getMock();
        $uploaderMock->method('save')->willReturn([
            'file' => 'file.ext',
            'tmp_name' => 'file.ext',
            'path' => 'path'
        ]);

        $uploaderFactoryMock->method('create')->willReturn($uploaderMock);
        /** @var \PHPUnit_Framework_MockObject_MockObject|StoreManagerInterface $storeManagerMock */
        $storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|Store $storeMock */
        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->method('getBaseUrl')->willReturn('http://example.com/');
        $storeManagerMock->method('getStore')->willReturn($storeMock);
        /** @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface $loggerMock */
        $loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->uploader = new UploaderModel(
            $coreFileStorageDatabaseMock,
            $fileSystemMock,
            $uploaderFactoryMock,
            $storeManagerMock,
            $loggerMock,
            [],
            self::BASE_TMP_PATH,
            self::BASE_PATH
        );
    }

    /**
     * @test Sample\News\Model\Uploader::getBaseTmpPath()
     */
    public function testGetBaseTmpPath()
    {
        $this->assertEquals(self::BASE_TMP_PATH, $this->uploader->getBaseTmpPath());
    }

    /**
     * @test Sample\News\Model\Uploader::getBasePath
     */
    public function testGetBasePath()
    {
        $this->assertEquals(self::BASE_PATH, $this->uploader->getBasePath());
    }

    /**
     * @test Sample\News\Model\Uploader::getAllowedExtensions
     */
    public function testGetAllowedExtensions()
    {
        $this->assertEquals([], $this->uploader->getAllowedExtensions());
        $this->uploader->setAllowedExtensions(['ext']);
        $this->assertEquals(['ext'], $this->uploader->getAllowedExtensions());
    }

    /**
     * @test Sample\News\Model\Uploader::getFilePath
     */
    public function testGetFilePath()
    {
        $this->assertEquals('path/here/file.ext', $this->uploader->getFilePath('path/here/', '/file.ext'));
        $this->assertEquals('path/here/file.ext', $this->uploader->getFilePath('path/here', '/file.ext'));
        $this->assertEquals('path/here/file.ext', $this->uploader->getFilePath('path/here/', 'file.ext'));
        $this->assertEquals('path/here/file.ext', $this->uploader->getFilePath('path/here', 'file.ext'));
    }

    /**
     * @test Sample\News\Model\Uploader::moveFileFromTmp
     */
    public function testMoveFileFromTmp()
    {
        $this->assertEquals('dummy', $this->uploader->moveFileFromTmp('dummy'));
    }

    /**
     * @test Sample\News\Model\Uploader::saveFileToTmpDir
     */
    public function testSaveFileToTmpDir()
    {
        $expected = [
            'file' => 'file.ext',
            'tmp_name' => 'file.ext',
            'path' => 'path',
            'url' => 'http://example.com/base/tmp/path/file.ext'
        ];
        $this->assertEquals($expected, $this->uploader->saveFileToTmpDir('dummy'));
    }

    /**
     * @test Sample\News\Model\Uploader::uploadFileAndGetName
     */
    public function testUploadFileAndGetName()
    {
        $data = [];
        $this->assertEmpty($this->uploader->uploadFileAndGetName('dummy', $data));
        $data = [
            'dummy' => [
                'delete' => 1,
                'dummy1' => [
                    'data1', 'data2'
                ]
            ]
        ];
        $this->assertEmpty($this->uploader->uploadFileAndGetName('dummy', $data));
        $data = [
            'dummy' => [
                [
                    'name' => 'file.ext',
                    'tmp_name' => 'dummy',
                    'file' => 'file.ext'
                ]
            ]
        ];
        $this->assertEquals('file.ext', $this->uploader->uploadFileAndGetName('dummy', $data));
    }
}
