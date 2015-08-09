<?php
class Picode_Portofoliu_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	* Checking if user is logged in or not
	* If not logged in then redirect to customer login
	*/
	// public function preDispatch()
	// {
	    // parent::preDispatch();
	    // if (!Mage::getSingleton('customer/session')->authenticate($this)) {
	        // $this->setFlag('', 'no-dispatch', true);
	    // }
	// }
	
	public function IndexAction()
	{
		$this -> loadLayout();
		$this -> getLayout() -> getBlock("head") -> setTitle($this -> __("Portofolii foto - video"));
		
		$breadcrumbs = $this -> getLayout() -> getBlock("breadcrumbs");
		$breadcrumbs -> addCrumb("home", array(
				"label" => $this -> __("Home Page"), 
				"title" => $this -> __("Home Page"), 
				"link" => Mage::getBaseUrl()
			)
		);
		$breadcrumbs -> addCrumb("portofoliu foto / video", 
			array(
				"label" => $this -> __("Portofoliu foto - video"), 
				"title" => $this -> __("Portofoliu foto - video")
			)
		);

		$this -> renderLayout();
	}
	
}
