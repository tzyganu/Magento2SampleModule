<?php
namespace Sample\News\Model\Routing;

use Sample\News\Model\FactoryInterface;

class Entity
{
    /**
     * @var string
     */
    protected $prefixConfigPath;
    /**
     * @var string
     */
    protected $suffixConfigPath;
    /**
     * @var string
     */
    protected $listKeyConfigPath;
    /**
     * @var string
     */
    protected $listAction;
    /**
     * @var FactoryInterface
     */
    protected $factory;
    /**
     * @var string
     */
    protected $controller;
    /**
     * @var string
     */
    protected $viewAction;
    /**
     * @var string
     */
    protected $param;

    /**
     * @param $prefixConfigPath
     * @param $suffixConfigPath
     * @param $listKeyConfigPath
     * @param FactoryInterface $factory
     * @param $controller
     * @param string $listAction
     * @param string $viewAction
     * @param string $param
     */
    public function __construct(
        $prefixConfigPath,
        $suffixConfigPath,
        $listKeyConfigPath,
        FactoryInterface $factory,
        $controller,
        $listAction = 'index',
        $viewAction = 'view',
        $param = 'id'
    ) {
        $this->prefixConfigPath     = $prefixConfigPath;
        $this->suffixConfigPath     = $suffixConfigPath;
        $this->listKeyConfigPath    = $listKeyConfigPath;
        $this->factory              = $factory;
        $this->controller           = $controller;
        $this->listAction           = $listAction;
        $this->viewAction           = $viewAction;
        $this->param                = $param;
    }

    /**
     * @return string
     */
    public function getPrefixConfigPath()
    {
        return $this->prefixConfigPath;
    }

    /**
     * @return string
     */
    public function getSuffixConfigPath()
    {
        return $this->suffixConfigPath;
    }

    /**
     * @return string
     */
    public function getListKeyConfigPath()
    {
        return $this->listKeyConfigPath;
    }

    /**
     * @return string
     */
    public function getListAction()
    {
        return $this->listAction;
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getViewAction()
    {
        return $this->viewAction;
    }

    /**
     * @return string
     */
    public function getParam()
    {
        return $this->param;
    }

}
