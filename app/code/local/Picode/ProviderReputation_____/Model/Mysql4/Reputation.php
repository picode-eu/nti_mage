<?php
class Picode_ProviderReputation_Model_Mysql4_Reputation extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('providerreputation/reputation', 'reputation_id');
    }
}