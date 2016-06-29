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
use Sample\News\Ui\Component\Listing\Column\AuthorActions;

class AuthorActionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test Sample\News\Ui\Component\Listing\Column\AuthorActions::prepareDataSource()
     */
    public function testPrepareDataSource()
    {
        $authorId = 1;
        /** @var \PHPUnit_Framework_MockObject_MockObject|UrlInterface $urlBuilderMock */
        $urlBuilderMock = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \PHPUnit_Framework_MockObject_MockObject|ContextInterface $contextMock */
        $contextMock = $this->getMockBuilder(ContextInterface::class)
            ->getMockForAbstractClass();
        /** @var \PHPUnit_Framework_MockObject_MockObject|Processor $processor */
        $processor = $this->getMockBuilder(Processor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects($this->any())->method('getProcessor')->willReturn($processor);
        /** @var \PHPUnit_Framework_MockObject_MockObject|UiComponentFactory $uiComponentFactoryMock */
        $uiComponentFactoryMock = $this->getMockBuilder(UiComponentFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Sample\News\Ui\Component\Listing\Column\AuthorActions $actions */
        $actions = new AuthorActions($contextMock, $uiComponentFactoryMock, $urlBuilderMock);
        // Define test input and expectations
        $items = [
            'data' => [
                'items' => [
                    [
                        'author_id' => $authorId
                    ]
                ]
            ]
        ];
        $name = 'item_name';
        $expectedItems = [
            [
                'author_id' => $authorId,
                $name => [
                    'edit' => [
                        'href' => 'some/url/edit',
                        'label' => __('Edit'),
                    ],
                    'delete' => [
                        'href' => 'some/url/delete',
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete "${ $.$data.name }"'),
                            'message' => __('Are you sure you wan\'t to delete the Author "${ $.$data.name }" ?')
                        ],
                    ]
                ],
            ]
        ];

        // Configure mocks and object data
        $urlBuilderMock->expects($this->any())
            ->method('getUrl')
            ->willReturnMap(
                [
                    [
                        AuthorActions::URL_PATH_EDIT,
                        [
                            'author_id' => $authorId
                        ],
                        'some/url/edit',
                    ],
                    [
                        AuthorActions::URL_PATH_DELETE,
                        [
                            'author_id' => $authorId
                        ],
                        'some/url/delete',
                    ],
                ]
            );

        $actions->setName($name);
        $items = $actions->prepareDataSource($items);
        // Run test
        $this->assertEquals($expectedItems, $items['data']['items']);
    }
}
