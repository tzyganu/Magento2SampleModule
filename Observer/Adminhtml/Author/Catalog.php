<?php
namespace Sample\News\Observer\Adminhtml\Author;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Sample\News\Model\ResourceModel\Author;

abstract class Catalog implements ObserverInterface
{
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var Author
     */
    protected $authorResource;
    /**
     * @var Registry
     */
    protected $coreRegistry;
    /**
     * @var JsHelper
     */
    protected $jsHelper;

    /**
     * @param Context $context
     * @param Author $authorResource
     * @param JsHelper $jsHelper
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        Author $authorResource,
        JsHelper $jsHelper,
        Registry $coreRegistry
    )
    {
        $this->context        = $context;
        $this->authorResource = $authorResource;
        $this->jsHelper       = $jsHelper;
        $this->coreRegistry   = $coreRegistry;
    }
}
