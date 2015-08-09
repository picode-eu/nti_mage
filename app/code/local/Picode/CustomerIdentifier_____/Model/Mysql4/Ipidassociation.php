<?php
class Picode_CustomerIdentifier_Model_Mysql4_Ipidassociation extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('customeridentifier/ipidassociation', 'id');
    }
}