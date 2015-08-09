<?php
class Picode_Portofoliu_Model_Observer
{
	/*
     * create a new layout handle
     * to be able to easily customize styling of pages or 
     * enable/disable specific blocks for the customer groups
     * 
     * we can use it in local.xml or any other layout file
    */
    public function addCustomerGroupHandle(Varien_Event_Observer $observer)
    {
        if (Mage::helper('customer')->isLoggedIn()) {
            /** @var $update Mage_Core_Model_Layout_Update */
            $update = $observer->getEvent()->getLayout()->getUpdate();
            $groupId = Mage::helper('customer')->getCustomer()->getGroupId();
            $groupName = Mage::getModel('customer/group')->load($groupId)->getCode();
            $update->addHandle('customer_group_' . str_replace(' ', '_', strtolower($groupName)));
        }
     
        return $this;
    }
}