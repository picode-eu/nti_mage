<?php

class Picode_ProviderReputation_Model_Mysql4_Loves extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('providerreputation/loves', 'love_id');
    }
}