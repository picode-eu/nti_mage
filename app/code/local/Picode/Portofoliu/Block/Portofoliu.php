<?php   
class Picode_Portofoliu_Block_Index extends Mage_Core_Block_Template
{   
	public function getAlbumColection()
	{
		return Mage::getModel('portofoliu/albums')->getCollection();
	}

	public function getVideoColection()
	{
		return Mage::getModel('portofoliu/videos')->getCollection();
	}
	
	public function getPhotoColection()
	{
		return Mage::getModel('portofoliu/photos')->getCollection();
	}


}