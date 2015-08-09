<?php
class Picode_Portofoliu_Model_Mysql4_Visitors extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("portofoliu/visitors", "visitor_id");
    }
}