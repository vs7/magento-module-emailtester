<?php

class VS7_EmailTester_Block_Adminhtml_Transaction_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/send', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $form->setHtmlIdPrefix('vs7_emailtester_');
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('transaction_form', array('legend' => Mage::helper('vs7_emailtester')->__('Transaction')));

        $fieldset->addField('id', 'hidden', array(
            'name' => 'id',
        ));

        $fieldset->addField('entity_id', 'text', array(
            'label' => Mage::helper('vs7_emailtester')->__('Entity ID'),
            'name' => 'entity_id'
        ));

        $fieldset->addField('template_id', 'select', array(
            'label'     => Mage::helper('vs7_emailtester')->__('Template'),
            'name'      => 'template_id',
            'options'    => Mage::helper('vs7_emailtester')->getEmailTemplates()
        ));

        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('vs7_emailtester')->__('Email Address'),
            'name' => 'email'
        ));

        return parent::_prepareForm();
    }
}