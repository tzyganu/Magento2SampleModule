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
use Magento\Store\Model\ScopeInterface;
use Sample\News\Model\Author;
use Sample\News\Model\Author\Url;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     *
     * dummy list url key
     */
    const LIST_URL_KEY = 'authors.html';
    /**
     * @var string
     *
     * dummy author view page url keu
     */
    const ITEM_URL_KEY = 'author';
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UrlInterface
     */
    protected $urlMockBuilder;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ScopeConfigInterface
     */
    protected $scopeConfigMock;
    /**
     * @var \Sample\News\Model\Author\Url
     */
    protected $urlModel;

    /**
     * setup tests
     */
    public function setUp()
    {
        $this->urlMockBuilder = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlMockBuilder->expects($this->any())
            ->method('getUrl')
            ->willReturnMap(
                [
                    [
                        '',
                        [
                            '_direct' => self::LIST_URL_KEY
                        ],
                        'http://example.com/'.self::LIST_URL_KEY,
                    ],
                    [
                        'sample_news/author/index',
                        null,
                        'http://example.com/sample_news/author/index'
                    ],
                    [
                        'sample_news/author/view',
                        ['id' => 1],
                        'http://example.com/sample_news/author/view/id/1'
                    ],
                    [
                        '',
                        [
                            '_direct' => self::ITEM_URL_KEY
                        ],
                        'http://example.com/'.self::ITEM_URL_KEY,
                    ],
                    [
                        '',
                        [
                            '_direct' => 'author/'.self::ITEM_URL_KEY
                        ],
                        'http://example.com/author/'.self::ITEM_URL_KEY,
                    ],
                    [
                        '',
                        [
                            '_direct' => 'author/'.self::ITEM_URL_KEY.'.html'
                        ],
                        'http://example.com/author/'.self::ITEM_URL_KEY.'.html',
                    ],
                    [
                        '',
                        [
                            '_direct' => self::ITEM_URL_KEY.'.html'
                        ],
                        'http://example.com/'.self::ITEM_URL_KEY.'.html',
                    ],
                ]
            );
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->urlModel = new Url($this->urlMockBuilder, $this->scopeConfigMock);
    }

    /**
     * @param bool $withUrlKey
     * @return \PHPUnit_Framework_MockObject_MockObject|\Sample\News\Model\Author
     */
    protected function setUpAuthor($withUrlKey)
    {
        $author = $this->getMockBuilder(Author::class)
            ->disableOriginalConstructor()
            ->getMock();
        $author->expects($this->any())->method('getId')->willReturn(1);
        if ($withUrlKey) {
            $author->expects($this->any())->method('getUrlKey')->willReturn(self::ITEM_URL_KEY);
        }
        return $author;
    }

    /**
     * @test \Sample\News\Model\Author\Url::getListUrl()
     *
     * with SEF url key
     */
    public function testGetListUrlWithSef()
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->willReturn(self::LIST_URL_KEY);
        $this->assertEquals('http://example.com/'.self::LIST_URL_KEY, $this->urlModel->getListUrl());
    }

    /**
     * @test \Sample\News\Model\Author\Url::getListUrl()
     *
     * without SEF url key
     */
    public function testGetListUrlWithoutSef()
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->willReturn('');
        $this->assertEquals('http://example.com/sample_news/author/index', $this->urlModel->getListUrl());
    }

    /**
     * @test \Sample\News\Model\Author\Url::getAuthorUrl()
     *
     * without SEF url key
     */
    public function testGetAuthorUrlWithoutSef()
    {
        $author = $this->setUpAuthor(false);
        $author->expects($this->any())->method('getUrlKey')->willReturn('');
        $this->assertEquals('http://example.com/sample_news/author/view/id/1', $this->urlModel->getAuthorUrl($author));
    }

    /**
     * @test \Sample\News\Model\Author\Url::getAuthorUrl()
     *
     * with SEF url key: no prefix and no suffix
     */
    public function testGetAuthorUrlWithSefNoSuffixNoPrefix()
    {
        $author = $this->setUpAuthor(true);

        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->willReturnMap(
                [
                    [
                        Url::URL_PREFIX_CONFIG_PATH,
                        ScopeInterface::SCOPE_STORE,
                        ''
                    ],
                    [
                        Url::URL_SUFFIX_CONFIG_PATH,
                        ScopeInterface::SCOPE_STORE,
                        ''
                    ],
                ]
            );
        $this->assertEquals('http://example.com/' . self::ITEM_URL_KEY, $this->urlModel->getAuthorUrl($author));
    }

    /**
     * @test \Sample\News\Model\Author\Url::getAuthorUrl()
     *
     * with SEF url key: with prefix and no suffix
     */
    public function testGetAuthorUrlWithSefNoSuffixWithPrefix()
    {
        $author = $this->setUpAuthor(true);
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->willReturnMap(
                [
                    [
                        Url::URL_PREFIX_CONFIG_PATH,
                        ScopeInterface::SCOPE_STORE,
                        null,
                        'author'
                    ],
                    [
                        Url::URL_SUFFIX_CONFIG_PATH,
                        ScopeInterface::SCOPE_STORE,
                        null,
                        ''
                    ],
                ]
            );
        $this->assertEquals('http://example.com/author/' . self::ITEM_URL_KEY, $this->urlModel->getAuthorUrl($author));
    }

    /**
     * @test \Sample\News\Model\Author\Url::getAuthorUrl()
     *
     * with SEF url key: no prefix and with suffix
     */
    public function testGetAuthorUrlWithSefWithSuffixNoPrefix()
    {
        $author = $this->setUpAuthor(true);
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->willReturnMap(
                [
                    [
                        Url::URL_PREFIX_CONFIG_PATH,
                        ScopeInterface::SCOPE_STORE,
                        null,
                        ''
                    ],
                    [
                        Url::URL_SUFFIX_CONFIG_PATH,
                        ScopeInterface::SCOPE_STORE,
                        null,
                        'html'
                    ],
                ]
            );
        $this->assertEquals('http://example.com/' . self::ITEM_URL_KEY . '.html', $this->urlModel->getAuthorUrl($author));
    }

    /**
     * @test \Sample\News\Model\Author\Url::getAuthorUrl()
     *
     * with SEF url key: with prefix and with suffix
     */
    public function testGetAuthorUrlWithSefWithSuffixWithPrefix()
    {
        $author = $this->setUpAuthor(true);
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->willReturnMap(
                [
                    [
                        Url::URL_PREFIX_CONFIG_PATH,
                        ScopeInterface::SCOPE_STORE,
                        null,
                        'author'
                    ],
                    [
                        Url::URL_SUFFIX_CONFIG_PATH,
                        ScopeInterface::SCOPE_STORE,
                        null,
                        'html'
                    ],
                ]
            );
        $this->assertEquals('http://example.com/author/' . self::ITEM_URL_KEY . '.html', $this->urlModel->getAuthorUrl($author));
    }
}
