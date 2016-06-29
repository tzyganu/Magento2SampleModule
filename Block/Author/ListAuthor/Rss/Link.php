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
namespace Sample\News\Block\Author\ListAuthor\Rss;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Sample\News\Model\Author\Rss as RssModel;

class Link extends Template
{
    /**
     * @var RssModel
     */
    protected $rssModel;

    /**
     * @param RssModel $rssModel
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        RssModel $rssModel,
        array $data = []
    ) {
        $this->rssModel = $rssModel;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function isRssEnabled()
    {
        return $this->rssModel->isRssEnabled();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return __('Subscribe to RSS Feed');
    }
    /**
     * @return string
     */
    public function getLink()
    {
        return $this->rssModel->getRssLink();
    }
}
