<?php
namespace Sample\News\Observer\Adminhtml\Author;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Sample\News\Model\ResourceModel\Author;

class SaveCategoryData extends Catalog implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $post = $this->context->getRequest()->getPostValue('category_sample_news_authors', -1);
        if ($post != '-1') {
            $post = json_decode($post, true);
            $category = $this->coreRegistry->registry('category');
            $this->authorResource->saveAuthorCategoryRelation($category, $post);
        }
        return $this;
    }
}
