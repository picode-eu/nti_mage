<?php
class Picode_ConturiFurnizori_RapoarteController extends Mage_Core_Controller_Front_Action
{
    /**
     * Check if customer is logged in or not
     * If not logged in then redirect to customer login
     */
    public function preDispatch()
    {
        parent::preDispatch();
     
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }
    
    /**
     * Get customer session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    protected function _checkFurnizoriGroup()
    {
        if ($this->_getSession()->getCustomer()->getGroupId() != '4') { // 4 is for furnizori
            $this->_redirect('customer/account/');
            return;
        }
        
        return;
    }
    
    public function avansateAction()
    {
        /**
         * Check if customer is furnizor or not
         * If not logged in then redirect to customer account dashboard
         */
        $this->_checkFurnizoriGroup();
        
        /**
         * Check if customer has this option available
         */
         if (!$this->_getSession()->getCustomer()->getAcOpRapoarteAvansate()) {
             $this->_redirect('customer/account/');
             return;
         }
        
        /**
         * continue if customer is furnizor
         */
        $this->loadLayout();
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        
        $this->getLayout()->getBlock('head')->setTitle($this->__('Rapoarte avansate'));
        $this->renderLayout();
    }
    
}