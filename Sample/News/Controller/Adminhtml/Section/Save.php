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
namespace Sample\News\Controller\Adminhtml\Section;

class Save extends \Sample\news\Controller\Adminhtml\Section {
    /**
     * Filter section data
     *
     * @param array $rawData
     * @return array
     */
    protected function _filterSectionPostData(array $rawData)
    {
        $data = $rawData;
        //TODO: add here any filtering
        return $data;
    }

    /**
     * Section save
     *
     * @return void
     */
    public function execute()
    {
        if (!($section = $this->_initSection())) {
            return;
        }

        $refreshTree = 'false';
        $data = $this->getRequest()->getPost();
        if ($data) {
            $section->addData($this->_filterSectionPostData($data['section']));
            if (!$section->getId()) {
                $parentId = $this->getRequest()->getParam('parent');
                if (!$parentId) {
                    $parentId = \Sample\News\Helper\Section::ROOT_SECTION_ID;
                }
                $parentSection = $this->_objectManager->create('Sample\News\Model\Section')->load($parentId);
                $section->setPath($parentSection->getPath());
            }

            if (isset($data['section_products']) && !$section->getProductsReadonly()) {
                $products = json_decode($data['section_products'], true);
                $section->setPostedProducts($products);
            }
            $this->_eventManager->dispatch(
                'sample_news_section_prepare_save',
                array('section' => $section, 'request' => $this->getRequest())
            );

            try {
                //TODO: maybe add validation

                if (isset($data['section']['entity_id'])) {
                    throw new \Magento\Framework\Model\Exception(__('Unable to save the section'));
                }
                $products = $this->getRequest()->getPost('section_products', -1);
                if ($products != -1) {
                    $products = json_decode($data['section_products'], true);
                    $section->setProductsData($products);
                }
                $section->save();
                $this->messageManager->addSuccess(__('Section was successfully saved.'));
                $refreshTree = 'true';
            } catch (\Exception $e) {
                echo $e->getMessage();exit;
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setSampleNewsSectionData($data);
                $refreshTree = 'false';
            }
        }

        if ($this->getRequest()->getPost('return_session_messages_only')) {
            $section->load($section->getId());
            // to obtain truncated section name

            /** @var $block \Magento\Framework\View\Element\Messages */
            $block = $this->_objectManager->get('Magento\Framework\View\Element\Messages');
            $block->setMessages($this->messageManager->getMessages(true));
            $body = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonEncode(
                    array(
                        'messages' => $block->getGroupedHtml(),
                        'error' => $refreshTree !== 'true',
                        'section' => $section->toArray()
                    )
                );
        } else {
            $url = $this->getUrl('sample_news/*/edit', array('_current' => true, 'id' => $section->getId()));
            $body = '<script type="text/javascript">parent.updateContent("' .
                $url .
                '", {}, ' .
                $refreshTree .
                ');</script>';
        }

        $this->getResponse()->setBody($body);
    }
}
