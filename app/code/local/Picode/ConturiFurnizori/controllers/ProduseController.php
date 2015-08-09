<?php
class Picode_ConturiFurnizori_ProduseController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    public function listAction()
    {
        $this->loadLayout();
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        
        $this->getLayout()->getBlock('head')->setTitle($this->__('Conturi furnizori'));
        $this->renderLayout();
    }
    
	public function viewAction(){
	    $this->loadLayout();
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        
        $this->getLayout()->getBlock('head')->setTitle($this->__('Plan View'));
        $this->renderLayout();
    }
}