<?php

class Picode_ProviderReputation_Model_Mysql4_Emails extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('providerreputation/emails', 'email_id');
    }
}