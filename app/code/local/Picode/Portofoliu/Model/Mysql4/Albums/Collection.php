<?php

class Picode_Portofoliu_Model_Mysql4_Albums_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		$this->_init("portofoliu/albums");
	}

	public function addAttributeToSelect($attr)
	{
		echo $attr;die();
	}

}
	 