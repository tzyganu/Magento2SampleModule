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
namespace Sample\News\Controller\Adminhtml\Article;

class MassStatus
    extends \Sample\News\Controller\Adminhtml\Article {
    /**
     * mass status change
     *
     * @return void
     */
    public function execute() {
        $articleIds = (array)$this->getRequest()->getParam('entity_ids');
        $status     = (int)$this->getRequest()->getParam('status');

        try {
            foreach ($articleIds as $id){
                $article = $this->_objectManager->get('Sample\News\Model\Article')
                    ->load($id);
                if ($article->getId()) {
                    $article->setStatus($status)->save();
                }
            }

            $this->messageManager->addSuccess(
                __('A total of %1 record(s) have been updated.', count($articleIds))
            );
        } catch (\Magento\Core\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Magento\Core\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()
                ->addException($e, __('Something went wrong while updating the article(s) status.'));
        }
        $this->_redirect('*/*/');
    }
}
