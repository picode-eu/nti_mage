<?php
class Picode_Portofoliu_Model_Mysql4_Albums extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("portofoliu/albums", "album_id");
    }
}