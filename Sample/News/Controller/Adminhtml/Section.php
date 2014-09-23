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
namespace Sample\News\Controller\Adminhtml;
class Section extends \Magento\Backend\App\Action {
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;
    /**
     * @var \Sample\News\Helper\Section
     */
    protected $_sectionHelper;

    /**
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Sample\News\Helper\Section $sectionHelper
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Sample\News\Helper\Section $sectionHelper,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_authSession = $authSession;
        $this->_sectionHelper = $sectionHelper;
        parent::__construct($context);
    }

    /**
     * @return \Sample\News\Model\Section
     */
    protected function _initSection() {
        $this->_title->add(__('Sections'));
        $sectionId = (int)$this->getRequest()->getParam('id', false);
        $section = $this->_objectManager->create('Sample\News\Model\Section');
        if ($sectionId) {
            $section->load($sectionId);
        }
        $activeTabId = (string)$this->getRequest()->getParam('active_tab_id');
        if ($activeTabId) {
            $this->_authSession->setSampleNewsSectionActiveTabId($activeTabId);
        }
        $this->_coreRegistry->register('sample_news_section', $section);
        //add category class to body so the tabs would look good
        $pageConfig = $this->_objectManager->get('Magento\Framework\View\Page\Config');
        $pageConfig->addBodyClass('catalog-category-edit');
        return $section;
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @return bool
     */
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Sample_News::sections');
    }
}

