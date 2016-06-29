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
namespace Sample\News\Test\Unit\Ui\DataProvider\Author\Form\Modifier;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Sample\News\Model\Author;
use Sample\News\Model\ResourceModel\Author\Collection;
use Sample\News\Model\ResourceModel\Author\CollectionFactory;
use Sample\News\Ui\DataProvider\Author\Form\Modifier\AuthorData;

class AuthorDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CollectionFactory
     */
    protected $collectionFactoryMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AuthorData
     */
    protected $authorDataModifier;
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * set up tests
     */
    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->collectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $authorData = [
            'author_id' => 1,
            'name' => 'test',
            'avatar' => '/some/image.jpg',
            'resume' => '/some/file.txt'
        ];

        $mockAuthor = $this->getMock(
            Author::class,
            [],
            [],
            '',
            false
        );
        $mockAuthor->method('getData')->willReturn($authorData);
        $mockAuthor->method('getId')->willReturn($authorData['author_id']);
        $mockAuthor->method('getAvatarUrl')->willReturn('http://example.com'.$authorData['avatar']);
        $mockAuthor->method('getAvatar')->willReturn($authorData['avatar']);
        $mockAuthor->method('getResumeUrl')->willReturn('http://example.com'.$authorData['resume']);
        $mockAuthor->method('getResume')->willReturn($authorData['resume']);

        $collectionMock = $this->objectManager->getCollectionMock(
            Collection::class,
            [$mockAuthor]
        );
        $collectionMock->method('getItems')->willReturn([$mockAuthor]);
        $this->collectionFactoryMock->method('create')->willReturn($collectionMock);
        $this->authorDataModifier = new AuthorData($this->collectionFactoryMock);
    }

    /**
     * @test Sample\News\Ui\DataProvider\Author\Form\Modifier\AuthorData::AuthorData::modfyMeta()
     */
    public function testModifyMeta()
    {
        $this->assertEquals(
            $this->getSampleData(),
            $this->authorDataModifier->modifyMeta($this->getSampleData())
        );
    }

    /**
     * @test modifyData method
     */
    public function testModifiyData()
    {
        $expected = [
            'key' => 'value',
            '1' => [
                'author_id' => 1,
                'name' => 'test',
                'avatar' => [
                    0 => [
                        'name' => '/some/image.jpg',
                        'url' => 'http://example.com/some/image.jpg'
                    ]
                ],
                'resume' => [
                    0 => [
                        'name' => '/some/file.txt',
                        'url' => 'http://example.com/some/file.txt'
                    ]
                ]
            ]
        ];
        $data = $this->authorDataModifier->modifyData($this->getSampleData());
        $this->assertEquals($expected, $data);
    }

    /**
     * @return array
     */
    protected function getSampleData()
    {
        return ['key' => 'value'];
    }
}
