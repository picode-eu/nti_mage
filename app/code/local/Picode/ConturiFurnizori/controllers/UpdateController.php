<?php
class Picode_ConturiFurnizori_UpdateController extends Mage_Checkout_Controller_Action
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
    
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
    
    public function platesteAction()
    {
        // get customer last order
        $furnizoriHelper = Mage::helper('conturifurnizori');
        $order = $furnizoriHelper->getCustomerLastOrder();
        // save new payment method
        if (!$order->getId()){
            Mage::getSingleton('customer/session')->addError('Ceva neașteptat s-a întâmplat . Te rugăm să încerci mai târziu.');
            $this->_redirect('customer/account/');
            return;
        }
        $payment = $order->getPayment();
        $payment->setMethod('ep_initialize');
        // save changes
        $payment->save();
        $order->save();
        
        // set last order session variable
        Mage::getSingleton('customer/session')->setLastIncrementId($order->getIncrementId());
        // rediret customer to EuPlatesc.ro
        $this->_redirect('ep/initialize/payment/');
        // all done for the moment
        return;
    }
    
    public function retrimitecerereaAction()
    {
        $this->loadLayout();
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        
        $this->getLayout()->getBlock('head')->setTitle($this->__('Solicita aprobarea'));
        $this->renderLayout();
    }
    
    /**
     * send notification email to admin
     */
    public function sendAprovelEmailAction()
    {
        $storeId = Mage::app()->getStore()->getId();
        // get customer last order
        $furnizoriHelper = Mage::helper('conturifurnizori');
        $order = $furnizoriHelper->getCustomerLastOrder();
        // get customer
        $customer = $this->getCustomer();
        
        $this->loadLayout();
        
         try {
            //load the custom template to the email 
            $emailTemplate = Mage::getModel('core/email_template')->loadDefault('aprove_free_acount');
            // it depends on the template variables
            $emailTemplateVariables = array();
            $emailTemplateVariables['account_level']   = $furnizoriHelper->getAccountTypeNameBylLevel('cont_level', $customer->getFurnizorAccountLevel())->getName();
            $emailTemplateVariables['account_date']    = date('d-m-Y H:i:s', strtotime($order->getCreatedAt()));
            $emailTemplateVariables['customer_name']   = $customer->getFirstname() . ' ' . $customer->getLastname();
            $emailTemplateVariables['customer_email']  = $customer->getEmail();
            $emailTemplateVariables['customer_id']     = $customer->getId();
            $message = strip_tags($this->getRequest()->getParam('private-message'));
            $emailTemplateVariables['private-message'] = $message != '' ? $message : 'No message.';
            
            //Zend_Debug::dump($emailTemplateVariables); die();
            
            $emailTemplate->setSenderName('Email Notifacation NTI');
            $emailTemplate->setSenderEmail('no-reply@nuntainimagini.ro');
            $emailTemplate->setType('html');
            $emailTemplate->setTemplateSubject('Aprobare Cont FREE');
            $emailTemplate->send(Mage::getStoreConfig('trans_email/ident_support/email'), Mage::getStoreConfig('trans_email/ident_support/name'), $emailTemplateVariables, $storeId);
            
            // set response
            $response = $this->getLayout()->createBlock('conturifurnizori/conturi_update');
            $response->setTemplate('picode/conturifurnizori/update/update_account.phtml');
            
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            return $this->getResponse($errorMessage);
        }
        
        $html =  $response->toHtml(); //also consider $response->renderView();     
        $this->getResponse()
             ->setHeader('Content-Type', 'text/html')
             ->setBody($html);
             
        return;
    }
    
    public function deblocheazaAction()
    {
        $this->loadLayout();
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        
        $this->getLayout()->getBlock('head')->setTitle($this->__('Solicita deblocarea'));
        $this->renderLayout();
    }
    
    public function reactiveazaAction()
    {
        $this->loadLayout();
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        
        $this->getLayout()->getBlock('head')->setTitle($this->__('Reactiveaza contul'));
        $this->renderLayout();
    }
    
}
