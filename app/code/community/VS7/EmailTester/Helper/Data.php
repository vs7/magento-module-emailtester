<?php

class VS7_EmailTester_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getEmailTemplates()
    {
        $collection = Mage::getResourceSingleton('core/email_template_collection');
        $storeId = Mage::app()->getStore()->getId();
        $guestTemplateId = Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
        $templateId = Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_TEMPLATE, $storeId);

        $templatesArray = array();

        foreach ($collection as $template) {
            if ($template->getId() == $templateId || $template->getId() == $guestTemplateId) {
                $templatesArray[$template->getId()] = $template->getTemplateCode();
            }
        }

        foreach ($collection as $template) {
            if ($template->getId() != $templateId && $template->getId() != $guestTemplateId) {
                $templatesArray[$template->getId()] = $template->getTemplateCode();
            }
        }

        return $templatesArray;
    }
}