<?php
require_once 'Mage/Customer/controllers/AccountController.php';

class Picode_CustomerIdentifier_Customer_AccountController extends Mage_Customer_AccountController
{
    public function __construct()
    {
        //die('in');
    }
    /**
     * Create customer account action
     */
    public function createPostAction()
    {
        //die('overwriten');
        /** @var $session Mage_Customer_Model_Session */
        $session = $this->_getSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if (!$this->getRequest()->isPost()) {
            $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
            $this->_redirectError($errUrl);
            return;
        }

        $customer = $this->_getCustomer();

        try {
            $errors = $this->_getCustomerErrors($customer);

            if (empty($errors)) {
                $customer->save();
                $this->_dispatchRegisterSuccess($customer);
                $this->_successProcessRegistration($customer);
                return;
            } else {
                $this->_addSessionError($errors);
            }
        } catch (Mage_Core_Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost());
            if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                $url = $this->_getUrl('customer/account/forgotpassword');
                $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
                $session->setEscapeMessages(false);
            } else {
                $message = $e->getMessage();
            }
            $session->addError($message);
        } catch (Exception $e) {
            $session->setCustomerFormData($this->getRequest()->getPost())
                ->addException($e, $this->__('Cannot save the customer.'));
        }
        $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
        $this->_redirectError($errUrl);
    }
    
    public function logoutAction()
    {
        //die('overwritten');
		$_customer = Mage::getSingleton('customer/session')->getCustomer();
		$customerIdentifier = $_customer->getCustomerIdentifier();
		
		$this->_getSession()->logout()->renewSession();
		
		$cookie = Mage::getSingleton('core/cookie')->set('cstidf', $customerIdentifier , time() + 31536000, '/');
        $session = Mage::getSingleton('customer/session')->setData('cstidf', $customerIdentifier);
		
		$this->_redirect('*/*/logoutSuccess');
    }
}
