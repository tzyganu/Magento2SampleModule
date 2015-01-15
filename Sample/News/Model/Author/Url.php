<?php
namespace Sample\News\Model\Author;

use Magento\Framework\UrlInterface;
use Sample\News\Model\Author;

class Url
{
    /**
     * url builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    public function getListUrl()
    {
        return $this->urlBuilder->getUrl('sample_news/author/index');
    }

    /**
     * @param Author $author
     * @return string
     */
    public function getAuthorUrl(Author $author)
    {
        if ($urlKey = $author->getUrlKey()) {
            return $this->urlBuilder->getUrl('', ['_direct'=>$urlKey]);
        }
        return $this->urlBuilder->getUrl('sample_news/author/view', ['id' => $author->getId()]);
    }
}
