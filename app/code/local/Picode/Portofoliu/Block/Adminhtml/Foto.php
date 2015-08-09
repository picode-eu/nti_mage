<?php

class Picode_Portofoliu_Block_Adminhtml_Foto extends Mage_Adminhtml_Block_Widget_Grid_Container
{

	public function __construct() {
		$this -> _controller = 'adminhtml_foto';
		$this -> _blockGroup = 'portofoliu';
		$this -> _headerText = Mage::helper('portofoliu') -> __('Portofolii Foto');
		$this -> _addButtonLabel = Mage::helper('portofoliu')->__('Add Item');
		parent::__construct();
	}

}
