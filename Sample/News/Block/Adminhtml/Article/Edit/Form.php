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
namespace Sample\News\Block\Adminhtml\Article\Edit;
class Form
    extends \Magento\Backend\Block\Widget\Form\Generic {

    /**
     * Prepare form
     * @return $this
     */
    protected function _prepareForm() {
        $form   = $this->_formFactory->create(array(
                'data' => array(
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                ))
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
