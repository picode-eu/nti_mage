<?php   
class Picode_Portofoliu_Block_Left extends Mage_Core_Block_Template
{
	private $_customerId = null;
	private $_albumCollection;
	private $_photoCollection;
	
	public function __construct()
	{
		$this->_customerId      = Mage::getSingleton('customer/session')->getCustomer()->getId();
		$this->_albumCollection = Mage::getModel('portofoliu/albums')->getCollection()->addFieldToFilter('customer_id', $this->_customerId);
		$this->_videoCollection = Mage::getModel('portofoliu/videos')->getCollection()->addFieldToFilter('customer_id', $this->_customerId);
		//Zend_Debug::dump($this->_albumCollection->getData()); die();
	}
	
	public function getAlbums($status)
	{
	    $albumCollection = Mage::getModel('portofoliu/albums')->getCollection()
	                               ->addFieldToFilter('customer_id', $this->_customerId)
		                           ->addFieldToFilter('is_visible', $status);
                                      
        return $albumCollection;
	}
	
	public function getVideos($status)
	{
		$videoCollection = Mage::getModel('portofoliu/videos')->getCollection()
	                               ->addFieldToFilter('customer_id', $this->_customerId)
		                           ->addFieldToFilter('is_visible', $status);
                                      
        return $videoCollection;
	}
	
	public function getPhotos()
	{
		return $this->_photoCollection = Mage::getModel('portofoliu/photos')->getCollection();
	}
	
	public function countPhoto($albumId)
	{
		$this->_photoCollection = $this->getPhotos();
		return $this->_photoCollection->addFieldToFilter('album_id', $albumId)->count();
	}
	
	public function shortenString($string, $width, $more = false)
	{
		if(strlen($string) > $width) {
	    	$string = wordwrap($string, $width);
	    	$string = substr($string, 0, strpos($string, "\n"));
			if($more) $string .= ' ...';
	  	}
	
	  	return $string;
	}

	// public function getContFurnizor()
	// {
		// $customerId = $this->_customerId;
// 		
  		// $oferteCollections = Mage::getModel('catalog/product')->getCollection()
				// ->addAttributeToSelect(array())
				// ->addFieldToFilter('attribute_set_id', array('eg' => 4))
				// ->addFieldToFilter('id_furnizor', array('eg' => $customerId));
// 		
		// $allProdIds = $oferteCollections->getAllIds();
// 		
		// foreach($allProdIds as $id){
			// return Mage::getModel('catalog/product')->load($id);
			// break;
		// }
	// }

}