<?php

class VS7_EmailTester_Adminhtml_EmailtesterController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $transactionsBlock = $this->getLayout()->createBlock('vs7_emailtester/adminhtml_transaction');
        $this->loadLayout()
            ->_title(Mage::helper('vs7_emailtester')->__('VS7 Email Tester - Transactions List'))
            ->_addContent($transactionsBlock)
            ->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__('VS7 Email Tester'))->_title($this->__('Transaction'));

        $transactionBlock = $this->getLayout()->createBlock('vs7_emailtester/adminhtml_transaction_edit');
        $this->loadLayout()
            ->_addContent($transactionBlock)
            ->renderLayout();
    }

    public function sendAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $id = $this->getRequest()->getParam('id');
            if ($id == 1) {
                $order = Mage::getModel('sales/order')->load($data['entity_id']);
            } else if ($id == 2) {
                $shipment = Mage::getModel('sales/order_shipment')->load($data['entity_id']);
                $order = $shipment->getOrder();
            } else if ($id == 3) {
                $creditMemo = Mage::getModel('sales/order_creditmemo')->load($data['entity_id']);
                $order = $creditMemo->getOrder();
            }

            if (!isset($order) || (!$order->getId() && $data['entity_id'])) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vs7_emailtester')->__('Order doesn`t exist.'));
                $this->_redirect('*/*/');
                return;
            }

            try {
                $mailer = Mage::getModel('core/email_template_mailer');
                $emailInfo = Mage::getModel('core/email_info');

                if ($order->getCustomerIsGuest()) {
                    $customerName = $order->getBillingAddress()->getName();
                } else {
                    $customerName = $order->getCustomerName();
                }

                $emailInfo->addTo($data['email'], $customerName);
                $mailer->addEmailInfo($emailInfo);
                $storeId = Mage::app()->getDefaultStoreView()->getId();
                $mailer->setSender(Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId));
                $mailer->setStoreId($storeId);
                $mailer->setTemplateId($data['template_id']);

                // Start store emulation process
                /** @var $appEmulation Mage_Core_Model_App_Emulation */
                $appEmulation = Mage::getSingleton('core/app_emulation');
                $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

                try {
                    // Retrieve specified view block from appropriate design package (depends on emulated store)
                    $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                        ->setIsSecureMode(true);
                    $paymentBlock->getMethod()->setStore($storeId);
                    $paymentBlockHtml = $paymentBlock->toHtml();
                } catch (Exception $exception) {
                    // Stop store emulation process
                    $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
                    throw $exception;
                }

                // Stop store emulation process
                $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

                if ($id == 1) { // Order
                    $mailer->setTemplateParams(array(
                        'order'        => $order,
                        'billing'      => $order->getBillingAddress(),
                        'payment_html' => $paymentBlockHtml
                    ));
                } else if ($id == 2) { // Shipment
                    $mailer->setTemplateParams(array(
                            'order'        => $order,
                            'shipment'     => $shipment,
                            'comment'      => '',
                            'billing'      => $order->getBillingAddress(),
                            'payment_html' => $paymentBlockHtml
                        )
                    );
                } else if ($id == 3) { // Credit Memo
                    $mailer->setTemplateParams(array(
                            'order'        => $order,
                            'creditmemo'   => $creditMemo,
                            'comment'      => '',
                            'billing'      => $order->getBillingAddress(),
                            'payment_html' => $paymentBlockHtml
                        )
                    );
                }

                $mailer->send();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vs7_emailtester')->__('Email has been sent.'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $id));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
}