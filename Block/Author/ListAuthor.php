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
namespace Sample\News\Block\Author;

use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Pager;
use Sample\News\Model\Author;
use Sample\News\Model\ResourceModel\Author\CollectionFactory as AuthorCollectionFactory;

class ListAuthor extends Template
{
    /**
     * @var AuthorCollectionFactory
     */
    protected $authorCollectionFactory;
    /**
     * @var UrlFactory
     */
    protected $urlFactory;

    /**
     * @var \Sample\News\Model\ResourceModel\Author\Collection
     */
    protected $authors;

    /**
     * @param Context $context
     * @param AuthorCollectionFactory $authorCollectionFactory
     * @param UrlFactory $urlFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        AuthorCollectionFactory $authorCollectionFactory,
        UrlFactory $urlFactory,
        array $data = []
    ) {
        $this->authorCollectionFactory = $authorCollectionFactory;
        $this->urlFactory = $urlFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Sample\News\Model\ResourceModel\Author\Collection
     */
    public function getAuthors()
    {
        if (is_null($this->authors)) {
            $this->authors = $this->authorCollectionFactory->create()
                ->addFieldToSelect('*')
                ->addFieldToFilter('is_active', Author::STATUS_ENABLED)
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->setOrder('name', 'ASC');
        }
        return $this->authors;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager $pager */
        $pager = $this->getLayout()->createBlock(Pager::class, 'sample_news.author.list.pager');
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
}
