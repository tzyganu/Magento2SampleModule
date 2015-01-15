<?php
namespace Sample\News\Block\Author;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\UrlFactory;
use Sample\News\Model\Resource\Author\CollectionFactory as AuthorCollectionFactory;

/**
 * @method \Sample\News\Model\Resource\Author\Collection getAuthors()
 * @method ListAuthor setAuthors(\Sample\News\Model\Resource\Author\Collection $authors)
 */
class ListAuthor extends Template
{

    protected $authorCollectionFactory;
    protected $urlFactory;


    /**
     * @param \Sample\News\Model\Resource\Author\CollectionFactory $authorCollectionFactory
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        AuthorCollectionFactory $authorCollectionFactory,
        UrlFactory $urlFactory,
        Context $context,
        array $data = []
    )
    {
        $this->authorCollectionFactory = $authorCollectionFactory;
        $this->urlFactory = $urlFactory;
        parent::__construct($context, $data);
    }

    /**
     * load the authors
     */
    protected  function _construct()
    {
        parent::_construct();
        /** @var \Sample\News\Model\Resource\Author\Collection $authors */
        $authors = $this->authorCollectionFactory->create()->addFieldToSelect('*')
            ->addFieldToFilter('is_active', 1)
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('name', 'ASC');
        $this->setAuthors($authors);
    }

    /**
     * @return bool
     */
    public function isRssEnabled()
    {
        return
            $this->_scopeConfig->getValue('rss/config/active', ScopeInterface::SCOPE_STORE) &&
            $this->_scopeConfig->getValue('sample_news/author/rss', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager $pager */
        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'sample_news.author.list.pager');
        $pager->setCollection($this->getAuthors());
        $this->setChild('pager', $pager);
        $this->getAuthors()->load();
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return string
     */
    public function getRssLink()
    {
        return $this->_urlBuilder->getUrl(
            'sample_news/author/rss',
            ['store' => $this->_storeManager->getStore()->getId()]
        );
    }
}
