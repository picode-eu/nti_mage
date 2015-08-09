<?php

class Picode_Popularity_Model_Mysql4_Offer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct(){
        $this->_init('popularity/offer');
    }
}
     