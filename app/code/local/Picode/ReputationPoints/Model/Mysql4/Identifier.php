<?php
class Picode_ReputationPoints_Model_Mysql4_Identifier extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('reputationpoints/identifier', 'identifier_id');
    }
}