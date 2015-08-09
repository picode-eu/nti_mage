<?php
class Picode_ReputationPoints_Model_Mysql4_Details extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('reputationpoints/details', 'rpp_details');
    }
}