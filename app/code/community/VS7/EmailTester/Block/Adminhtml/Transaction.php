<?php

class VS7_EmailTester_Block_Adminhtml_Transaction extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_transaction';
        $this->_headerText = Mage::helper('vs7_emailtester')->__('VS7 Email Tester - Transactions List');
        $this->_blockGroup = 'vs7_emailtester';

        parent::__construct();

        $this->removeButton('add');
    }
}