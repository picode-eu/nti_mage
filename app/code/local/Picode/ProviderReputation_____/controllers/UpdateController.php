<?php
class Picode_ProviderReputation_UpdateController extends Mage_Core_Controller_Front_Action
{
    public function reputationAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->loadLayout();
            $this->renderLayout();
        }
        
        return;
    }
	
    public function contactsAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->loadLayout();
            $this->renderLayout();
        }
        
        return;
    }
    
    public function lovesAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->loadLayout();
            $this->renderLayout();
        }
        
        return;
    }
    
    public function sendemailAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->loadLayout();
            $this->renderLayout();
        }
        
        return;
    }
}
