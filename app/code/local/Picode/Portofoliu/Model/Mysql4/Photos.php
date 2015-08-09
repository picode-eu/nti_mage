<?php
class Picode_Portofoliu_Model_Mysql4_Photos extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("portofoliu/photos", "photo_id");
    }
}