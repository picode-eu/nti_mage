<?php
class Picode_Conturifurnizori_Model_Resource_Usermessage extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('conturifurnizori/usermessage', 'message_id');
    }
}