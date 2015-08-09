<?php

class Picode_Popularity_Model_Mysql4_Portfolio extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('popularity/portfolio', 'popularity_id');
    }
}