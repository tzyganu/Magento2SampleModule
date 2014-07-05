<?php
namespace Sample\News\Block\Adminhtml\Article\Edit;
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * Prepare form
     * @access protected
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
