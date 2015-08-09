<?php
class Picode_ReputationPoints_Model_Mysql4_Frdemails extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('reputationpoints/frdemails', 'rpp_frdemails');
    }
}