<?php
class Picode_ReputationPoints_UpdateController extends Mage_Core_Controller_Front_Action
{
    public function reputationAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->loadLayout();
            $this->renderLayout();
        }
        
        return;
    }

    public function socialAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->loadLayout();
            $this->renderLayout();
        }

        return;
    }

    public function sendAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->loadLayout();
            $this->renderLayout();
        }

        return;
    }

    public function contactviewAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->loadLayout();
            $this->renderLayout();
        }

        return;
    }
    
}