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

namespace Sample\News\Test\Unit\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponent\Processor;
use Sample\News\Model\Uploader;
use Sample\News\Ui\Component\Listing\Column\AuthorActions;
use Sample\News\Ui\Component\Listing\Column\Avatar;

class AvatarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test \Sample\News\Ui\Component\Listing\Column\Avatar::prepareDataSource()
     */
    public function testPrepareDataSource()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|ContextInterface $contextMock */
        $contextMock = $this->getMockBuilder(ContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|UiComponentFactory $uiComponentFactoryMock */
        $uiComponentFactoryMock = $this->getMockBuilder(UiComponentFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|UrlInterface $urlBuilderMock */
        $urlBuilderMock = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|Uploader $uploaderMock */
        $uploaderMock = $this->getMockBuilder(Uploader::class)
            ->disableOriginalConstructor()
            ->getMock();
        $uploaderMock->method('getBasePath')->willReturn('/base/path');
        $uploaderMock->method('getBaseUrl')->willReturn('http://example.com');
        $processor = $this->getMockBuilder(Processor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects($this->any())->method('getProcessor')->willReturn($processor);

        /** @var \Sample\News\Ui\Component\Listing\Column\Avatar $avatarModel */
        $avatarModel = new Avatar($contextMock, $uiComponentFactoryMock, $urlBuilderMock, $uploaderMock, [], []);
        $authorId = 1;
        $urlBuilderMock
            ->method('getUrl')
            ->willReturnMap(
                [
                    [
                        AuthorActions::URL_PATH_EDIT,
                        [
                            'author_id' => $authorId
                        ],
                        'some/url/here',
                    ],
                ]
            );
        $fieldName = 'avatar';
        $avatarModel->setName($fieldName);
        $items = [
            'data' => [
                'items' => [
                    [
                        'author_id' => $authorId,
                        $fieldName => '/some/image.jpg',
                        'name' => 'test name',
                    ]
                ]
            ]
        ];
        $expectedResult = [
            'data' => [
                'items' => [
                    [
                        'author_id' => $authorId,
                        $fieldName => '/some/image.jpg',
                        'name' => 'test name',
                        $fieldName . '_src' => 'http://example.com/base/path/some/image.jpg',
                        $fieldName . '_alt' => 'test name',
                        $fieldName . '_link' => 'some/url/here',
                        $fieldName . '_orig_src' => 'http://example.com/base/path/some/image.jpg'
                    ]
                ]
            ]
        ];
        $items = $avatarModel->prepareDataSource($items);
        $this->assertEquals($expectedResult, $items);
    }
}
