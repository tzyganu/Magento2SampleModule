<?php
/**
 * Sample_News extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Sample
 * @package        Sample_News
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Sample\News\Controller;

class Router
    implements \Magento\Framework\App\RouterInterface {
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $_actionFactory;
    /**
     * Event manager
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Store manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Article factory
     * @var \Sample\News\Model\ArticleFactory
     */
    protected $_articleFactory;

    /**
     * Config primary
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Url
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Response
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\State $appState
     * @param \Sample\News\Model\ArticleFactory $articleFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\State $appState,
        \Sample\News\Model\ArticleFactory $articleFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->_actionFactory = $actionFactory;
        $this->_eventManager = $eventManager;
        $this->_url = $url;
        $this->_appState = $appState;
        $this->_articleFactory = $articleFactory;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Validate and Match News Article and modify request
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request) {
        if (!$this->_appState->isInstalled()) {
            $this->_response->setRedirect($this->_url->getUrl('install'))->sendResponse();
            exit;
        }
        $identifier = trim($request->getPathInfo(), '/');
        $condition = new \Magento\Framework\Object(array('identifier' => $identifier, 'continue' => true));
        $this->_eventManager->dispatch(
            'sample_news_controller_router_match_before',
            array('router' => $this, 'condition' => $condition)
        );
        $identifier = $condition->getIdentifier();
        if ($condition->getRedirectUrl()) {
            $this->_response->setRedirect($condition->getRedirectUrl());
            $request->setDispatched(true);
            return $this->_actionFactory->createController(
                'Magento\Framework\App\Action\Redirect',
                array('request' => $request)
            );
        }
        if (!$condition->getContinue()) {
            return null;
        }

        $settings = array();
        $settings['article'] = array(
            'prefix'        => $this->_scopeConfig->getValue(\Sample\News\Model\Article::XML_URL_PREFIX_PATH),
            'suffix'        => $this->_scopeConfig->getValue(\Sample\News\Model\Article::XML_URL_SUFFIX_PATH),
            'list_key'      => $this->_scopeConfig->getValue(\Sample\News\Helper\Article::LIST_PATH),
            'list_action'   => 'index',
            'model_factory' => $this->_articleFactory,
            'controller'    => 'article',
            'action'        => 'view',
            'param'         => 'id',
            'check_path'    => 0
        );

        foreach ($settings as $entity => $settings) {
            if ($settings['list_key']) {
                if ($settings['list_key'] == $identifier) {
                    $request->setModuleName('sample_news')
                        ->setControllerName($settings['controller'])
                        ->setActionName($settings['list_action']);
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
                    return $this->_actionFactory->create(
                        'Magento\Framework\App\Action\Forward',
                        array('request' => $request)
                    );
                }
            }
            if ($settings['prefix']){
                $parts = explode('/', $identifier);
                if ($parts[0] != $settings['prefix'] || count($parts) != 2){
                    return null;
                }
                $identifier = $parts[1];
            }
            if ($settings['suffix']){
                $identifier = substr($identifier, 0 , -strlen($settings['suffix']) - 1);
            }
            /** @var \Sample\News\Model\ArticleFactory $articleFactory */
            $articleFactory = $settings['model_factory'];
            $article = $articleFactory->create();
            $id = $article->checkIdentifier($identifier, $this->_storeManager->getStore()->getId());
            if (!$id) {
                return null;
            }
            $request->setModuleName('sample_news')
                ->setControllerName($settings['controller'])
                ->setActionName($settings['action'])
                ->setParam($settings['param'], $id);
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
            return $this->_actionFactory->create(
                'Magento\Framework\App\Action\Forward',
                array('request' => $request)
            );
        }
        return null;
    }
}
