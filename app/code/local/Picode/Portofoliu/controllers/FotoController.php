<?php
class Picode_Portofoliu_FotoController extends Mage_Core_Controller_Front_Action
{
	public function IndexAction()
	{
		$this -> loadLayout();
		$this -> getLayout() -> getBlock("head") -> setTitle($this -> __("Portofolii foto"));
		
		$breadcrumbs = $this -> getLayout() -> getBlock("breadcrumbs");
		$breadcrumbs -> addCrumb("home", array(
				"label" => $this -> __("Home Page"), 
				"title" => $this -> __("Home Page"), 
				"link" => Mage::getBaseUrl()
			)
		);
		$breadcrumbs -> addCrumb("portofoliu foto", 
			array(
				"label" => $this -> __("Portofoliu foto"), 
				"title" => $this -> __("Portofoliu foto")
			)
		);

		$this -> renderLayout();
	}
	
	public function ViewAction()
	{
		$this -> loadLayout();
		$this -> getLayout() -> getBlock("head") -> setTitle($this -> __("Portofolii foto"));
		
		$breadcrumbs = $this -> getLayout() -> getBlock("breadcrumbs");
		$breadcrumbs -> addCrumb("home", array(
				"label" => $this -> __("Home Page"), 
				"title" => $this -> __("Home Page"), 
				"link" => Mage::getBaseUrl()
			)
		);
		$breadcrumbs -> addCrumb("portofoliu foto", 
			array(
				"label" => $this -> __("Portofoliu foto"), 
				"title" => $this -> __("Portofoliu foto")
			)
		);

		$this -> renderLayout();
	}
}