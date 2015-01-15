<?php
namespace Sample\News\Block\Author\ListAuthor;

use Magento\Framework\View\Element\AbstractBlock;
use Sample\News\Model\Author\Rss as RssModel;
use Sample\News\Model\Author\Url;
use Magento\Framework\View\Element\Context;
use Sample\News\Model\Resource\Author\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Rss\DataProviderInterface;
use Magento\Store\Model\ScopeInterface;

class Rss extends AbstractBlock implements DataProviderInterface
{
    /**
     * @var string
     */
    const CACHE_LIFETIME_CONFIG_PATH = 'sample_news/author/rss_cache';

    /**
     * @var \Sample\News\Model\Author\Rss
     */
    protected $rssModel;

    /**
     * @var \Sample\News\Model\Author\Url
     */
    protected $urlModel;

    /**
     * @var \Sample\News\Model\Resource\Author\CollectionFactory
     */
    protected $authorCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param RssModel $rssModel
     * @param Url $urlModel
     * @param CollectionFactory $authorCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        RssModel $rssModel,
        Url $urlModel,
        CollectionFactory $authorCollectionFactory,
        StoreManagerInterface $storeManager,
        Context $context,
        array $data = []
    )
    {
        $this->rssModel = $rssModel;
        $this->urlModel = $urlModel;
        $this->authorCollectionFactory = $authorCollectionFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        $storeId = (int)$this->getRequest()->getParam('store_id');
        if ($storeId == null) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        return $storeId;
    }

    /**
     * @return array
     */
    public function getRssData()
    {
        $url = $this->urlModel->getListUrl();
        $data = ['title' => __('Authors'), 'description' => __('Authors'), 'link' => $url, 'charset' => 'UTF-8'];


        $collection = $this->authorCollectionFactory->create();
        $collection->addStoreFilter($this->getStoreId());
        $collection->addFieldToFilter('is_active', 1); //TODO: use constant
        $collection->addFieldToFilter('in_rss', 1); //TODO: use constant
        foreach ($collection as $item) {
            /** @var \Sample\News\Model\Author $item */
            //TODO: add more attributes to RSS
            $description = '<table><tr><td><a href="%s">%s</a></td></tr></table>';
            $description = sprintf($description, $item->getAuthorUrl(), $item->getName());
            $data['entries'][] = [
                'title' => $item->getName(),
                'link' => $item->getAuthorUrl(),
                'description' => $description,
            ];
        }
        return $data;
    }

    /**
     * Check if RSS feed allowed
     *
     * @return mixed
     */
    public function isAllowed()
    {
        return $this->rssModel->isRssEnabled();
    }

    /**
     * Get information about all feeds this Data Provider is responsible for
     *
     * @return array
     */
    public function getFeeds()
    {
        $feeds = [];
        $feeds[] = [
            'label' => __('Authors'),
            'link' => $this->rssModel->getRssLink(),
        ];
        $result = ['group' => __('News'), 'feeds' => $feeds];
        return $result;
    }

    /**
     * @return bool
     */
    public function isAuthRequired()
    {
        return false;
    }

    /**
     * @return int
     */
    public function getCacheLifetime()
    {
        $lifetime = $this->_scopeConfig->getValue(
            self::CACHE_LIFETIME_CONFIG_PATH,
            ScopeInterface::SCOPE_STORE
        );
        return $lifetime ?: null;
    }
}
