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

use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Sample\News\Model\Author;
use Sample\News\Model\Author\Url;
use Sample\News\Model\Output;
use Sample\News\Model\Uploader;
use Sample\News\Model\UploaderPool;

class AuthorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * @var Author
     */
    protected $authorModel;

    /**
     * setup mocks
     */
    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        /** @var \PHPUnit_Framework_MockObject_MockObject|Output $outputMock */
        $outputMock = $this->getMockBuilder(Output::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject||UploaderPool $uploaderPoolMock */
        $uploaderPoolMock = $this->getMockBuilder(UploaderPool::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \PHPUnit_Framework_MockObject_MockObject|Uploader $uploaderMock */
        $uploaderMock = $this->getMockBuilder(Uploader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $uploaderMock->method('getBaseUrl')->willReturn('http://example/com/');
        $uploaderMock->method('getBasePath')->willReturn('base/path');
        $uploaderPoolMock->method('getUploader')->willReturn($uploaderMock);

        /** @var \PHPUnit_Framework_MockObject_MockObject|Context $contextMock */
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|Registry $registryMock */
        $registryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|FilterManager $filterManagerMock */
        $filterManagerMock = $this->getMockBuilder(FilterManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $filterManagerMock->method('translitUrl')->willReturn('dummy');
        /** @var \PHPUnit_Framework_MockObject_MockObject||Url $urlMock */
        $urlMock = $this->getMockBuilder(Url::class)
            ->disableOriginalConstructor()
            ->getMock();
        $urlMock->method('getUrl')->willReturn('http://example.com/dummy');

        $this->authorModel = $this->objectManager->getObject(
            Author::class,
            [
                'context' => $contextMock,
                'registry' => $registryMock,
                'output' => $outputMock,
                'uploaderPool' => $uploaderPoolMock,
                'filterManager' => $filterManagerMock,
                'url' => $urlMock
            ]
        );
        $data = $this->getAuthorData();
        $this->authorModel->setData($data);
    }

    /**
     * @return array
     */
    protected function getAuthorData()
    {
        $data = [
            'name' => 'John Doe',
            'type' => '1',
            'awards' => [1, 2],
            'in_rss' => 1,
            'is_active' => 1,
            'country' => 'RO',
            'biography' => '<p>Some biography</p>',
            'dob' => '1983-08-18',
            'url_key' => 'john-doe',
            'avatar' => '/path/to/avatar.jpg',
            'resume' => 'path/to/resume.pdf',
            'created_at' => '2016-06-06 00:12:34',
            'updated_at' => '2016-06-08 12:34:56',
            'author_id' => 1,
            'meta_title' => 'dummy meta title',
            'meta_description' => 'dummy meta description',
            'meta_keywords' => 'dummy meta keywords',
            'store_id' => [0],
        ];
        return $data;
    }

    /**
     * @test class getters
     */
    public function testGetters()
    {
        $data = $this->getAuthorData();
        $this->assertEquals($data['name'], $this->authorModel->getName());
        $this->assertEquals($data['awards'], $this->authorModel->getAwards());
        $this->assertEquals($data['in_rss'], $this->authorModel->getInRss());
        $this->assertEquals($data['is_active'], $this->authorModel->getIsActive());
        $this->assertEquals($data['is_active'], $this->authorModel->isActive());
        $this->assertEquals($data['country'], $this->authorModel->getCountry());
        $this->assertEquals($data['biography'], $this->authorModel->getBiography());
        $this->assertEquals($data['dob'], $this->authorModel->getDob());
        $this->assertEquals($data['url_key'], $this->authorModel->getUrlKey());
        $this->assertEquals($data['avatar'], $this->authorModel->getAvatar());
        $this->assertEquals($data['resume'], $this->authorModel->getResume());
        $this->assertEquals($data['created_at'], $this->authorModel->getCreatedAt());
        $this->assertEquals($data['updated_at'], $this->authorModel->getUpdatedAt());
        $this->assertEquals($data['meta_title'], $this->authorModel->getMetaTitle());
        $this->assertEquals($data['meta_description'], $this->authorModel->getMetaDescription());
        $this->assertEquals($data['meta_keywords'], $this->authorModel->getMetaKeywords());
        $this->assertEquals($data['store_id'], $this->authorModel->getStoreId());
    }

    /**
     * @test \Sample\News\Model\Author::getAvatarUrl()
     *
     * with null avatar
     */
    public function testGetAvatarUrlWithNull()
    {
        $this->authorModel->setAvatar(null);
        $this->assertFalse($this->authorModel->getAvatarUrl());
    }

    /**
     * @test \Sample\News\Model\Author::getAvatarUrl()
     *
     * with string avatar
     */
    public function testGetAvatarUrlWithString()
    {
        $this->authorModel->setAvatar('/avatar.jpg');
        $this->assertEquals('http://example/com/base/path/avatar.jpg', $this->authorModel->getAvatarUrl());
    }

    /**
     * @test \Sample\News\Model\Author::getAvatarUrl()
     *
     * with exception
     */
    public function testGetAvatarUrlWithException()
    {
        $this->authorModel->setAvatar(['dummy']);
        $this->setExpectedException('\Exception', __('Something went wrong while getting the avatar url.'));
        $this->authorModel->getAvatarUrl();
    }

    /**
     * @test \Sample\News\Model\Author::getResumeUrl()
     *
     * with null resume
     */
    public function testGetResumeUrlWithNull()
    {
        $this->authorModel->setResume(null);
        $this->assertFalse($this->authorModel->getResumeUrl());
    }

    /**
     * @test \Sample\News\Model\Author::getResumeUrl()
     *
     * with string resume
     */
    public function testGetResumeUrlWithString()
    {
        $this->authorModel->setResume('/resume.jpg');
        $this->assertEquals('http://example/com/base/path/resume.jpg', $this->authorModel->getResumeUrl());
    }

    /**
     * @test \Sample\News\Model\Author::getResumeUrl()
     *
     * with exception
     */
    public function testGetResumeUrlWithException()
    {
        $this->authorModel->setResume(['dummy']);
        $this->setExpectedException('\Exception', __('Something went wrong while getting the resume url.'));
        $this->authorModel->getResumeUrl();
    }
}
