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



use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Sample\News\Model\Author\Rss;

class RssTest extends \PHPUnit_Framework_TestCase
{
    const STORE_ID = 1;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UrlInterface
     */
    protected $urlMockBuilder;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ScopeConfigInterface
     */
    protected $scopeConfigMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StoreManagerInterface
     */
    protected $storeManagerMock;
    /**
     * @var Rss
     */
    protected $rss;

    /**
     * setup tests
     */
    protected function setUp()
    {
        $this->urlMockBuilder = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlMockBuilder
            ->expects($this->any())
            ->method('getUrl')
            ->willReturnMap(
                [
                    [
                        'sample_news/author/rss',
                        [
                            'store' => self::STORE_ID
                        ],
                        'some/url/here'
                    ]
                ]
            );
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->expects($this->any())->method('getId')->willReturn(self::STORE_ID);
        $this->storeManagerMock->expects($this->any())->method('getStore')->willReturn($storeMock);

        $this->rss = new Rss(
            $this->urlMockBuilder,
            $this->scopeConfigMock,
            $this->storeManagerMock
        );
    }

    /**
     * @test \Sample\News\Model\Author\Rss::isRssEnabled()
     *
     * with global rss setting disabled
     * and author rss disabled
     */
    public function testIsRssEnabledGlobalFalseAuthorFalse()
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->willReturnMap(
                [
                    [
                        'rss/config/active',
                        ScopeInterface::SCOPE_STORE,
                        null,
                        false
                    ],
                    [
                        'sample_news/author/rss',
                        ScopeInterface::SCOPE_STORE,
                        null,
                        false
                    ],
                ]
            );
        $this->assertFalse($this->rss->isRssEnabled());
    }

    /**
     * @test \Sample\News\Model\Author\Rss::isRssEnabled()
     *
     * with global rss setting enabled
     * and author rss disabled
     */
    public function testIsRssEnabledGlobalTrueAuthorFalse()
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->willReturnMap(
                [
                    [
                        'rss/config/active',
                        ScopeInterface::SCOPE_STORE,
                        null,
                        true
                    ],
                    [
                        'sample_news/author/rss',
                        ScopeInterface::SCOPE_STORE,
                        null,
                        false
                    ],
                ]
            );
        $this->assertFalse($this->rss->isRssEnabled());
    }

    /**
     * @test \Sample\News\Model\Author\Rss::isRssEnabled()
     *
     * with global rss setting disabled
     * and author rss enabled
     */
    public function testIsRssEnabledGlobalFalseAuthorTrue()
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->willReturnMap(
                [
                    [
                        'rss/config/active',
                        ScopeInterface::SCOPE_STORE,
                        null,
                        false
                    ],
                    [
                        'sample_news/author/rss',
                        ScopeInterface::SCOPE_STORE,
                        null,
                        true
                    ],
                ]
            );
        $this->assertFalse($this->rss->isRssEnabled());
    }

    /**
     * @test \Sample\News\Model\Author\Rss::isRssEnabled()
     *
     * with global rss setting enabled
     * and author rss enabled
     */
    public function testIsRssEnabledGlobalTrueAuthorTrue()
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->willReturnMap(
                [
                    [
                        'rss/config/active',
                        ScopeInterface::SCOPE_STORE,
                        null,
                        true
                    ],
                    [
                        'sample_news/author/rss',
                        ScopeInterface::SCOPE_STORE,
                        null,
                        true
                    ],
                ]
            );
        $this->assertTrue($this->rss->isRssEnabled());
    }

    /**
     * @test \Sample\News\Model\Author\Rss::isRssEnabled()
     */
    public function testGetRssLink()
    {
        $this->assertEquals('some/url/here', $this->rss->getRssLink());
    }
}
