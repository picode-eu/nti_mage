<?php

class Picode_Popularity_Model_Offer extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('popularity/offer');
    }
}