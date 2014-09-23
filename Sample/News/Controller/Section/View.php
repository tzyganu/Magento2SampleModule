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
namespace Sample\News\Controller\Section;
class View
    extends \Sample\News\Controller\Section {
    /**
     * $init the article
     * access protected
     * @return bool|\Sample\News\Model\Section
     */
    protected function _initSection(){
        $sectionId   = $this->getRequest()->getParam('id', 0);
        $section     = $this->_objectManager->create('Sample\News\Model\Section')
            ->setStoreId($this->_storeManager->getStore()->getId())
            ->load($sectionId);
        if (!$section->getId()){
            return false;
        }
        elseif (!$section->getStatus()){
            return false;
        }
        elseif (!$section->getStatusPath()) {
            return false;
        }
        return $section;
    }

    /**
     * view action
     */
    public function execute() {
        $section = $this->_initSection();
        if (!$section) {
            $this->_forward('no-route');
            return;
        }
        $this->_coreRegistry->register('current_section', $section);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        if ($root = $this->_view->getLayout()->getBlock('root')) {
            $root->addBodyClass('news-section news-section-' . $section->getId());
        }
        if ($this->_sectionHelper->getUseBreadcrumbs() && $breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs')){
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->_storeManager->getStore()->getBaseUrl()
            ])
            ->addCrumb('sections',[
                'label' => __('Sections'),
                'title' => __('Sections'),
                'link'  => $this->_sectionHelper->getSectionsUrl(),
            ]);
            $parents = $section->getParentSections();
            foreach ($parents as $parent){
                if ($parent->getId() != $this->_sectionHelper->getRootSectionId() && $parent->getId() != $section->getId()){
                    $breadcrumbs->addCrumb('section-'.$parent->getId(), array(
                        'label'    => $parent->getName(),
                        'link'    => $link = $parent->getSectionUrl(),
                    ));
                }
            }
            $breadcrumbs->addCrumb('section', array(
                'label'    => $section->getName(),
                'link'    => '',
            ));
        }
        $headBlock = $this->_view->getLayout()->getBlock('head');
        if ($headBlock) {
            if ($section->getMetaTitle()){
                $headBlock->setTitle($section->getMetaTitle());
            }
            else{
                $headBlock->setTitle($section->getName());
            }
            $headBlock->setKeywords($section->getMetaKeywords());
            $headBlock->setDescription($section->getMetaDescription());
        }
        $titleBlock = $this->_view->getLayout()->getBlock('page.main.title');
        if ($titleBlock) {
            $titleBlock->setPageTitle($section->getName());
        }
        $this->_view->renderLayout();
    }
}
