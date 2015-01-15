<?php
namespace Sample\News\Model;

use Sample\News\Model\Author\Url as AuthorUrl;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\App\Request\Http;

class Observer
{
    protected $request;
    protected $authorUrl;

    public function __construct(
        Http $request,
        AuthorUrl $authorUrl
    )
    {
        $this->request = $request;
        $this->authorUrl = $authorUrl;
    }

    /**
     * @param $observer
     * @return $this
     */
    public function addLinksToTopMenu(EventObserver $observer)
    {
        /** @var \Magento\Framework\Data\Tree\Node $menu */
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $fullAction = $this->request->getFullActionName();
        $selectedActions = ['sample_news_author_index', 'sample_news_author_view'];
        $authorNodeId = 'authors';
        $data = [
            'name'      => __('Authors'),
            'id'        => $authorNodeId,
            'url'       => $this->authorUrl->getListUrl(),
            'is_active' => in_array($fullAction, $selectedActions)
        ];
        $authorsNode = new Node($data, 'id', $tree, $menu);
        $menu->addChild($authorsNode);
        return $this;
    }
}
