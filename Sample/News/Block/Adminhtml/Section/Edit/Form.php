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

namespace Sample\News\Block\Adminhtml\Section\Edit;

use Magento\Backend\Block\Template;

class Form
    extends \Sample\News\Block\Adminhtml\Section\AbstractSection {
    /**
     * Additional buttons on category page
     *
     * @var array
     */
    protected $_additionalButtons = array();

    /**
     * @var string
     */
    protected $_template = 'section/edit/form.phtml';

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Catalog\Model\Resource\Category\Tree $categoryTree
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory,
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Sample\News\Model\Resource\Section\Tree $sectionTree,
        \Magento\Framework\Registry $registry,
        \Sample\News\Model\SectionFactory $sectionFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        parent::__construct($context, $sectionTree, $registry, $sectionFactory, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        /** @var \Sample\News\Model\Section $section */
        $section = $this->getSection();
        $sectionId = (int)$section->getId();

        $this->setChild(
            'tabs',
            $this->getLayout()->createBlock('Sample\News\Block\Adminhtml\Section\Tabs', 'tabs')
        );

        // Save button
        $this->addButton(
            'save',
            array(
                'id' => 'save',
                'label' => __('Save Section'),
                'onclick' => "sectionSubmit('" . $this->getSaveUrl() . "', true)",
                'class' => 'save primary save-section'
            )
        );

        // Delete button
        if ($sectionId && $section->isDeleteable()) {
            $this->addButton(
                'delete',
                array(
                    'id' => 'delete',
                    'label' => __('Delete Section'),
                    'onclick' => "sectionDelete('" . $this->getUrl(
                        'sample_news/*/delete',
                        array('_current' => true)
                    ) . "', true, {$sectionId})",
                    'class' => 'delete'
                )
            );
        }

        // Reset button
        $resetPath = $sectionId ? 'sample_news/*/edit' : 'sample_news/*/add';
        $this->addButton(
            'reset',
            array(
                'id' => 'reset',
                'label' => __('Reset'),
                'onclick' => "sectionReset('" . $this->getUrl($resetPath, array('_current' => true)) . "',true)",
                'class' => 'reset'
            )
        );

        return parent::_prepareLayout();
    }

    /**
     * @return string
     * @deprecated
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * @return string
     * @deprecated
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * @return string
     * @deprecated
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Retrieve additional buttons html
     *
     * @return string
     */
    public function getAdditionalButtonsHtml()
    {
        $html = '';
        foreach ($this->_additionalButtons as $childName) {
            $html .= $this->getChildHtml($childName);
        }
        return $html;
    }

    /**
     * Add additional button
     *
     * @param string $alias
     * @param array $config
     * @return $this
     */
    public function addAdditionalButton($alias, $config)
    {
        if (isset($config['name'])) {
            $config['element_name'] = $config['name'];
        }
        if ($this->hasToolbarBlock()) {
            $this->addButton($alias, $config);
        } else {
            $this->setChild(
                $alias . '_button',
                $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->addData($config)
            );
            $this->_additionalButtons[$alias] = $alias . '_button';
        }

        return $this;
    }

    /**
     * Remove additional button
     *
     * @param string $alias
     * @return $this
     */
    public function removeAdditionalButton($alias)
    {
        if (isset($this->_additionalButtons[$alias])) {
            $this->unsetChild($this->_additionalButtons[$alias]);
            unset($this->_additionalButtons[$alias]);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        if ($this->getSectionId()) {
            return $this->getSectionName();
        } else {
            $parentId = (int)$this->getRequest()->getParam('parent');
            if ($parentId && $parentId != \Sample\News\Helper\Section::ROOT_SECTION_ID) {
                return __('New Child Section');
            } else {
                return __('New Root Section');
            }
        }
    }

    /**
     * @param array $args
     * @return string
     */
    public function getDeleteUrl(array $args = array())
    {
        $params = array('_current' => true);
        $params = array_merge($params, $args);
        return $this->getUrl('sample_news/*/delete', $params);
    }

    /**
     * Return URL for refresh input element 'path' in form
     *
     * @param array $args
     * @return string
     */
    public function getRefreshPathUrl(array $args = array())
    {
        $params = array('_current' => true);
        $params = array_merge($params, $args);
        return $this->getUrl('sample_news/*/refreshPath', $params);
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->_request->isXmlHttpRequest() || $this->_request->getParam('isAjax');
    }

    /**
     * Add button block as a child block or to global Page Toolbar block if available
     *
     * @param string $buttonId
     * @param array $data
     * @return $this
     */
    protected function addButton($buttonId, array $data)
    {
        $childBlockId = $buttonId . '_button';
        $button = $this->getButtonChildBlock($childBlockId);
        $button->setData($data);
        $block = $this->getLayout()->getBlock('page.actions.toolbar');
        if ($block) {
            $block->setChild($childBlockId, $button);
        } else {
            $this->setChild($childBlockId, $button);
        }
    }

    /**
     * @return bool
     */
    protected function hasToolbarBlock()
    {
        return $this->getLayout()->isBlock('page.actions.toolbar');
    }

    /**
     * Adding child block with specified child's id.
     *
     * @param string $childId
     * @param null|string $blockClassName
     * @return \Magento\Backend\Block\Widget
     */
    protected function getButtonChildBlock($childId, $blockClassName = null)
    {
        if (null === $blockClassName) {
            $blockClassName = 'Magento\Backend\Block\Widget\Button';
        }
        return $this->getLayout()->createBlock($blockClassName, $this->getNameInLayout() . '-' . $childId);
    }

    public function getProductsJson()
    {
        $products = $this->getSection()->getProductsPosition();
        if (!empty($products)) {
            return $this->_jsonEncoder->encode($products);
        }
        return '{}';
    }
}
