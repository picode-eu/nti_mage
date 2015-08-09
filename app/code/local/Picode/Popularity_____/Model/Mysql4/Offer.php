<?php

class Picode_Popularity_Model_Mysql4_Offer extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('popularity/offer', 'popularity_id');
    }
}