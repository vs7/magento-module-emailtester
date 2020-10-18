<?php

class VS7_EmailTester_Block_Adminhtml_Transaction_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('vs7_emailtesterTransactionGrid');
        $this->setDefaultSort('transaction_id');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = new Varien_Data_Collection();
        $templates = array(
            array(
                'id' => 1,
                'transaction' => 'Order Confirmation'
            ),
            array(
                'id' => 2,
                'transaction' => 'Shipment Confirmation'
            ),
            array(
                'id' => 3,
                'transaction' => 'Credit Memo'
            )
        );

        foreach ($templates as $template) {
            $rowObj = new Varien_Object();
            $rowObj->setData($template);
            $collection->addItem($rowObj);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('transaction',
            array(
                'header' => $this->__('Transaction'),
                'index' => 'transaction'
            )
        );
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}