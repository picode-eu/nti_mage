<?php
class Picode_Portofoliu_Model_Mysql4_Videos extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("portofoliu/videos", "video_id");
    }
}