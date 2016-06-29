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
namespace Sample\News\Controller;

use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\Action\Redirect;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\State;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Url;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Sample\News\Model\Routing\Entity;

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
     * @var \Magento\Framework\App\ResponseInterface|\Magento\Framework\App\Response\Http
     */
    protected $response;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var bool
     */
    protected $dispatched;

    /**
     * @var \Sample\News\Model\Routing\Entity[]
     */
    protected $routingEntities;

    /**
     * @param ActionFactory $actionFactory
     * @param ManagerInterface $eventManager
     * @param UrlInterface $url
     * @param State $appState
     * @param StoreManagerInterface $storeManager
     * @param ResponseInterface $response
     * @param ScopeConfigInterface $scopeConfig
     * @param array $routingEntities
     */
    public function __construct(
        ActionFactory $actionFactory,
        ManagerInterface $eventManager,
        UrlInterface $url,
        State $appState,
        StoreManagerInterface $storeManager,
        ResponseInterface $response,
        ScopeConfigInterface $scopeConfig,
        array $routingEntities
    ) {
        $this->actionFactory    = $actionFactory;
        $this->eventManager     = $eventManager;
        $this->url              = $url;
        $this->appState         = $appState;
        $this->storeManager     = $storeManager;
        $this->response         = $response;
        $this->scopeConfig      = $scopeConfig;
        $this->routingEntities  = $routingEntities;
    }

    /**
     * Validate and Match News Author and modify request
     *
     * @param \Magento\Framework\App\RequestInterface|\Magento\Framework\HTTP\PhpEnvironment\Request $request
     * @return bool
     */
    public function match(RequestInterface $request)
    {
        if (!$this->dispatched) {
            $urlKey = trim($request->getPathInfo(), '/');
            $origUrlKey = $urlKey;
            /** @var Object $condition */
            $condition = new DataObject(['url_key' => $urlKey, 'continue' => true]);
            $this->eventManager->dispatch(
                'sample_news_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
            );
            $urlKey = $condition->getUrlKey();
            if ($condition->getRedirectUrl()) {
                $this->response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);
                return $this->actionFactory->create(Redirect::class);
            }
            if (!$condition->getContinue()) {
                return null;
            }
            foreach ($this->routingEntities as $entityKey => $entity) {
                $match = $this->matchRoute($request, $entity, $urlKey, $origUrlKey);
                if ($match === false) {
                    continue;
                }
                return $match;
            }
        }
        return null;
    }

    /**
     * @param RequestInterface|\Magento\Framework\HTTP\PhpEnvironment\Request $request
     * @param Entity $entity
     * @param $urlKey
     * @param $origUrlKey
     * @return bool|\Magento\Framework\App\ActionInterface|null
     */
    protected function matchRoute(RequestInterface $request, Entity $entity, $urlKey, $origUrlKey)
    {
        $listKey = $this->scopeConfig->getValue($entity->getListKeyConfigPath(), ScopeInterface::SCOPE_STORE);
        if ($listKey) {
            if ($urlKey == $listKey) {
                $request->setModuleName('sample_news');
                $request->setControllerName($entity->getController());
                $request->setActionName($entity->getListAction());
                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);
                $this->dispatched = true;
                return $this->actionFactory->create(Forward::class);
            }
        }
        $prefix = $this->scopeConfig->getValue($entity->getPrefixConfigPath(), ScopeInterface::SCOPE_STORE);
        if ($prefix) {
            $parts = explode('/', $urlKey);
            if ($parts[0] != $prefix || count($parts) != 2) {
                return false;
            }
            $urlKey = $parts[1];
        }
        $configSuffix = $this->scopeConfig->getValue($entity->getSuffixConfigPath(), ScopeInterface::SCOPE_STORE);
        if ($configSuffix) {
            $suffix = substr($urlKey, -strlen($configSuffix) - 1);
            if ($suffix != '.'.$configSuffix) {
                return false;
            }
            $urlKey = substr($urlKey, 0, -strlen($configSuffix) - 1);
        }
        $instance = $entity->getFactory()->create();
        $id = $instance->checkUrlKey($urlKey, $this->storeManager->getStore()->getId());
        if (!$id) {
            return null;
        }
        $request->setModuleName('sample_news');
        $request->setControllerName($entity->getController());
        $request->setActionName($entity->getViewAction());
        $request->setParam($entity->getParam(), $id);
        $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
        $request->setDispatched(true);
        $this->dispatched = true;
        return $this->actionFactory->create(Forward::class);
    }
}
