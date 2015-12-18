<?php
namespace Sample\News\Controller\Author;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Sample\News\Model\AuthorFactory;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Sample\News\Model\Author\Url as UrlModel;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class View extends Action
{
    const BREADCRUMBS_CONFIG_PATH = 'sample_news/author/breadcrumbs';
    /**
     * @var \Sample\News\Model\AuthorFactory
     */
    protected $authorFactory;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Sample\News\Model\Author\Url
     */
    protected $urlModel;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Context $context
     * @param AuthorFactory $authorFactory
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     * @param Registry $coreRegistry
     * @param UrlModel $urlModel
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        AuthorFactory $authorFactory,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        UrlModel $urlModel,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->authorFactory = $authorFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->urlModel = $urlModel;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $authorId = (int) $this->getRequest()->getParam('id');
        $author = $this->authorFactory->create();
        $author->load($authorId);

        if (!$author->isActive()) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }

        $this->coreRegistry->register('current_author', $author);

        $resultPage = $this->resultPageFactory->create();
        $title = ($author->getMetaTitle()) ?: $author->getName();
        $resultPage->getConfig()->getTitle()->set($title);
        $resultPage->getConfig()->setDescription($author->getMetaDescription());
        $resultPage->getConfig()->setKeywords($author->getMetaKeywords());
        if ($this->scopeConfig->isSetFlag(self::BREADCRUMBS_CONFIG_PATH, ScopeInterface::SCOPE_STORE)) {
            /** @var \Magento\Theme\Block\Html\Breadcrumbs $breadcrumbsBlock */
            $breadcrumbsBlock = $resultPage->getLayout()->getBlock('breadcrumbs');
            if ($breadcrumbsBlock) {
                $breadcrumbsBlock->addCrumb(
                    'home',
                    [
                        'label' => __('Home'),
                        'link'  => $this->_url->getUrl('')
                    ]
                );
                $breadcrumbsBlock->addCrumb(
                    'authors',
                    [
                        'label' => __('Authors'),
                        'link'  => $this->urlModel->getListUrl()
                    ]
                );
                $breadcrumbsBlock->addCrumb(
                    'author-'.$author->getId(),
                    [
                        'label' => $author->getName()
                    ]
                );
            }
        }

        return $resultPage;
    }
}
