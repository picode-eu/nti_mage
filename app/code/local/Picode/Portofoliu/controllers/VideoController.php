<?php
class Picode_Portofoliu_VideoController extends Mage_Core_Controller_Front_Action
{
	public function IndexAction()
	{
		$this->loadLayout();
		$this->getLayout()->getBlock("head")->setTitle($this->__("Portofolii video | NuntaInImagini.ro"));
		
		$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
		$breadcrumbs->addCrumb("home", array(
				"label" => $this->__("Home Page"), 
				"title" => $this->__("Home Page"), 
				"link" => Mage::getBaseUrl()
			)
		);
		$breadcrumbs->addCrumb("portofoliu-foto",
			array(
				"label" => $this->__("Videoclipuri"),
				"title" => $this->__("Videoclipuri")
			)
		);

		$this->renderLayout();
	}
	
	public function ViewAction()
	{
		$params = $this->getRequest()->getParams();
		$i = 0;
		foreach ($params as $key => $val) {
			if ($i == 0) {
				$params['provider_id'] = (int)$val;
				unset($params[$key]);
			}
			if ($i == 1) {
				$params['id'] = $val;
				unset($params[$key]);
			}
			$i++;
		}

		$_video = Mage::getModel('portofoliu/videos')->load($params['id']);
		$_provider = Mage::getModel('customer/customer')->load($params['provider_id']);

		if (!$_video->getId()) {
			$this->_forward('defaultNoRoute');
			return;
		}

		$this->loadLayout();
		$this->getLayout()->getBlock("head")->setTitle($this->__(strip_tags($_video->getVideoName())) . ' | ' . strip_tags($_provider->getBusinessDescriptionsTitle()));
		$this->getLayout()->getBlock("head")->setDescription(Mage::helper('portofoliu')->stringTruncate($_video->getDescriptions(), 200));

		$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
		$breadcrumbs->addCrumb("home", array(
				"label" => $this->__("Home Page"), 
				"title" => $this->__("Home Page"), 
				"link" => Mage::getBaseUrl()
			)
		);
		$breadcrumbs->addCrumb("portofoliu-video",
			array(
				"label" => $this->__("Videoclipuri"),
				"title" => $this->__("Videoclipuri"),
				"link" => Mage::getBaseUrl() . 'portofoliu/video/'
			)
		);

		$breadcrumbs->addCrumb("portofoliu-foto",
			array(
				"label" => $this->__(strip_tags($_video->getVideoName())),
				"title" => $this->__(strip_tags($_video->getVideoName()))
			)
		);

		$this->renderLayout();
	}
}