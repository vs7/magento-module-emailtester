<?php

class VS7_EmailTester_Block_Adminhtml_Transaction_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_transaction';
        parent::__construct();

        $this->_blockGroup = 'vs7_emailtester';
        $this->_mode = 'edit';

        $this->_addButton('sendandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Send and Continue'),
            'onclick'   => 'sendAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function sendAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";

        $this->removeButton('reset');
        $this->removeButton('delete');

        $this->updateButton('save', 'label', Mage::helper('adminhtml')->__('Send'));
    }
}