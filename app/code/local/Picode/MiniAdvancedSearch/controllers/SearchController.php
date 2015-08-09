<?php
class Picode_MiniAdvancedSearch_SearchController extends Mage_Core_Controller_Front_Action
{
    public function AdvancedAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->loadLayout();
            $this->renderLayout();
        }

        return;
    }

    public function quickresultsAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->loadLayout();
            $this->renderLayout();
        }

        return;
    }

    public function providerAction()
    {
        //if ($this->getRequest()->isPost()) {
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle('Rezultatele cautarii FURNIZORI');
            $this->renderLayout();
        //}

        return;
    }
}