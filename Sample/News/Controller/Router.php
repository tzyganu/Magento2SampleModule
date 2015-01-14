<?php
namespace Sample\News\Controller;

use \Magento\Framework\App\RouterInterface;
use \Magento\Framework\App\ActionFactory;
use \Magento\Framework\Object;
use \Magento\Framework\Event\ManagerInterface;
use \Magento\Framework\UrlInterface;
use \Magento\Framework\App\State;
use \Sample\News\Model\AuthorFactory;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\App\ResponseInterface;
use \Magento\Framework\App\RequestInterface;
use \Magento\Framework\Url;

class Router implements RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Store manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Author factory
     * @var \Sample\News\Model\AuthorFactory
     */
    protected $authorFactory;

    /**
     * Config primary
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * Url
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * Response
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @param ActionFactory $actionFactory
     * @param ManagerInterface $eventManager
     * @param UrlInterface $url
     * @param State $appState
     * @param AuthorFactory $authorFactory
     * @param StoreManagerInterface $storeManager
     * @param ResponseInterface $response
     */
    public function __construct(
        ActionFactory $actionFactory,
        ManagerInterface $eventManager,
        UrlInterface $url,
        State $appState,
        AuthorFactory $authorFactory,
        StoreManagerInterface $storeManager,
        ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->url = $url;
        $this->appState = $appState;
        $this->authorFactory = $authorFactory;
        $this->storeManager = $storeManager;
        $this->response = $response;
    }

    /**
     * Validate and Match News Author and modify request
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     * //TODO: maybe remove this and use the url rewrite table.
     */
    public function match(RequestInterface $request)
    {
        $urlKey = trim($request->getPathInfo(), '/');
        /** @var Object $condition */
        $condition = new Object(['url_key' => $urlKey, 'continue' => true]);
        $this->eventManager->dispatch(
            'sample_news_controller_router_match_before',
            ['router' => $this, 'condition' => $condition]
        );
        $urlKey = $condition->getUrlKey();
        if ($condition->getRedirectUrl()) {
            $this->response->setRedirect($condition->getRedirectUrl());
            $request->setDispatched(true);
            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Redirect',
                ['request' => $request]
            );
        }
        if (!$condition->getContinue()) {
            return null;
        }
        $author = $this->authorFactory->create();
        $id = $author->checkUrlKey($urlKey, $this->storeManager->getStore()->getId());
        if (!$id) {
            return null;
        }
        $request->setModuleName('sample_news')
            ->setControllerName('author')
            ->setActionName('view')
            ->setParam('id', $id);
        $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);
        $request->setDispatched(true);
        return $this->actionFactory->create(
            'Sample\News\Controller\Author\View',
            ['request' => $request]
        );
    }
}
