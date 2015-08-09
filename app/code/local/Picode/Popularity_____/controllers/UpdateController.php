<?php
class Picode_Popularity_UpdateController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        die('index');
    }
    
    public function loveAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->loadLayout();
            $this->renderLayout();
        }
        
        return;
    }
    
    public function contactAction()
    {
        if (
            $this->getRequest()->isPost() &&  $this->getRequest()->getParam('is_ajax')) {
            $this->loadLayout();
            $this->renderLayout();
        }
        
        return;
    }
}
