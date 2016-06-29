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
namespace Sample\News\Test\Unit\Ui\Component\Listing\Column\Store;

use Magento\Framework\Escaper;
use Magento\Store\Model\Group;
use Magento\Store\Model\Store as StoreModel;
use Magento\Store\Model\System\Store;
use Magento\Store\Model\Website;
use Sample\News\Ui\Component\Listing\Column\Store\Options;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Options
     */
    protected $options;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Store
     */
    protected $systemStoreMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Website
     */
    protected $websiteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Group
     */
    protected $groupMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StoreModel
     */
    protected $storeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Escaper
     */
    protected $escaperMock;

    /**
     * setup tests
     */
    protected function setUp()
    {

        $this->systemStoreMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->websiteMock = $this->getMock(
            Website::class,
            ['getId', 'getName'],
            [],
            '',
            false
        );
        $this->groupMock = $this->getMock(Group::class, [], [], '', false);
        $this->storeMock = $this->getMock(StoreModel::class, [], [], '', false);
        $this->escaperMock = $this->getMock(Escaper::class, [], [], '', false);
        $this->options = new Options($this->systemStoreMock, $this->escaperMock);
    }

    /**
     * @test Sample\News\Ui\Component\Listing\Column\Store\Options::toOptionArray()
     */
    public function testToOptionArray()
    {
        $websiteCollection = [$this->websiteMock];
        $groupCollection = [$this->groupMock];
        $storeCollection = [$this->storeMock];

        $expectedOptions = [
            [
                'label' => __('All Store Views'),
                'value' => '0'
            ],
            [
                'label' => 'Main Website',
                'value' => [
                    [
                        'label' => '    Main Website Store',
                        'value' => [
                            [
                                'label' => '        Default Store View',
                                'value' => '1'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->systemStoreMock->expects($this->once())->method('getWebsiteCollection')->willReturn($websiteCollection);
        $this->systemStoreMock->expects($this->once())->method('getGroupCollection')->willReturn($groupCollection);
        $this->systemStoreMock->expects($this->once())->method('getStoreCollection')->willReturn($storeCollection);

        $this->websiteMock->expects($this->atLeastOnce())->method('getId')->willReturn('1');
        $this->websiteMock->expects($this->any())->method('getName')->willReturn('Main Website');

        $this->groupMock->expects($this->atLeastOnce())->method('getWebsiteId')->willReturn('1');
        $this->groupMock->expects($this->atLeastOnce())->method('getId')->willReturn('1');
        $this->groupMock->expects($this->atLeastOnce())->method('getName')->willReturn('Main Website Store');

        $this->storeMock->expects($this->atLeastOnce())->method('getGroupId')->willReturn('1');
        $this->storeMock->expects($this->atLeastOnce())->method('getName')->willReturn('Default Store View');
        $this->storeMock->expects($this->atLeastOnce())->method('getId')->willReturn('1');

        $this->escaperMock->expects($this->atLeastOnce())->method('escapeHtml')->willReturnMap(
            [
                ['Default Store View', null, 'Default Store View'],
                ['Main Website Store', null, 'Main Website Store'],
                ['Main Website', null, 'Main Website']
            ]
        );

        $this->assertEquals($expectedOptions, $this->options->toOptionArray());
    }
}
