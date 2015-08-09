<?php
class Picode_ConturiFurnizori_MesajeController extends Mage_Core_Controller_Front_Action
{
    /**
     * Check if customer is logged in or not
     * If not logged in then redirect to customer login
     */
    // public function preDispatch()
    // {
        // parent::preDispatch();
//      
        // if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            // $this->setFlag('', 'no-dispatch', true);
        // }
    // }
    
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
    
    public function casutapostalaAction()
    {
        /**
         * Check if customer is furnizor or not
         * If not logged in then redirect to customer account dashboard
         */
        // $this->_checkFurnizoriGroup();
        
        /**
         * Check if customer has this option available
         */
         // if (!$this->_getSession()->getCustomer()->getAcOpNotificariEmail()) {
             // $this->_redirect('customer/account/');
             // return;
         // }
        
        /**
         * continue if customer is furnizor and is enabled notifications 
         */
        
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/');
            return;
        }
        
        Mage::getSingleton('catalog/layer')->setReplyTo('');
         
        $this->loadLayout();
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        
        $this->getLayout()->getBlock('head')->setTitle($this->__('Casuta postala'));
        $this->renderLayout();
    }
    
    public function detaliiAction()
    {
        /**
         * Check if customer is furnizor or not
         * If not logged in then redirect to customer account dashboard
         */
        // $this->_checkFurnizoriGroup();
        
        /**
         * Check if customer has this option available
         */
         // if (!$this->_getSession()->getCustomer()->getAcOpNotificariEmail()) {
             // $this->_redirect('customer/account/');
             // return;
         // }
         
         /**
          * check if the customer has rights to read selected message
          */
          
         if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
             $this->_redirect('customer/account/');
             return;
         }
          
         $messageId = $this->getRequest()->getParam('id');
         $customerId = $this->_getSession()->getCustomer()->getId();
         $message = Mage::getModel('conturifurnizori/usermessage')->load($messageId);
          
         $permisions = array($message->getReceverId(), $message->getSenderId());
         
         if (!in_array($customerId, $permisions)) {
             $this->_redirect('conturifurnizori/mesaje/casutapostala/');
             return;
         }
        
         /**
         * continue, everything seems to be OK 
         */
        $this->loadLayout();
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        
        $this->getLayout()->getBlock('head')->setTitle($this->__('Detalii mesaj'));
        $this->renderLayout();
    }
		 
	public function newmessageAction()
	{
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('');
            return;
        }
        
		$params = $this->getRequest()->getParams();

        $_product = !isset($params['is_profile']) && isset($params['offer_id']) ? $_product = Mage::getModel('catalog/product')->load($params['offer_id']) : false;
		$_customer = $this->_getSession()->getCustomer();
        //Zend_Debug::dump($params); die();
        
        $customerId = $_customer->getId() ? $_customer->getId() : '0';
        $customerFirstname = $params['sender_firstname'];
        $customerLastname = $params['sender_lastname'];
        $providerId = $params['provider_id'];
		$provider = Mage::getModel('customer/customer')->load($providerId);
        
        // check if is allowed to send new email
        $now = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
        $customerIdentifier = $customerId ? $_customer->getCustomerIdentifier() : Mage::helper('reputationpoints')->getCustomerIdentifier();
        $update = 'ctcemail';
        
        $emailsSent = Mage::getModel('reputationpoints/details')
            ->getCollection()
            ->addFieldToFilter('customer_identifier', $customerIdentifier)
            //->addFieldToFilter('rpp_type', $rppType)
            //->addFieldToFilter('entity_id', $entityId)
            ->addFieldToFilter('updated_rpp', $update)
            ->addFieldToFilter('created_at', array(
                'from' => date('Y-m-d H:i:s', strtotime($now) - 3600),
                'to' => $now,
                'date' => true, // specifies conversion of comparison values
            ))
        ;

        $existing = $emailsSent->getLastItem();
        $emailsSentCount = (int)$emailsSent->getSize();
        $maxEmailsAllowed = (int)Mage::helper('reputationpoints')->getModuleConfig('rpp_email', 'email_allowed');

        if ($emailsSentCount >= $maxEmailsAllowed) {
            $notAllowed = true;
        } else {
            $notAllowed = false;
        }
        if (!$notAllowed) {
            $newMessage = Mage::getModel('conturifurnizori/usermessage')
                   ->setSenderId($customerId)
                   ->setReceverId($providerId)
                   ->setSenderFirstname($customerFirstname)
                   ->setSenderLastname($customerLastname)
                   ->setSubject($params['subject'])
                   ->setMessage($params['message'])
                   ->setReplayTo($params['replay_to'])
                   ->setIsFirst(1)
                   ->save();
               
       //Zend_Debug::dump($newMessage->getData()); die();
       
       
           // send transactional email
           $emailSent = $this->_sendNotificationEmail($newMessage, false, $_product);
       } else {
           // the email cannot be send cause of a admin limitation
           $emailSent = false;
       }
       
       if ($emailSent) {
           Mage::getSingleton('core/session')->addSuccess('Mesajul către <strong>' . $provider->getBusinessDescriptionsTitle() . '</strong> a fost trimis cu succes.');
       } else {
           Mage::getSingleton('core/session')->addError('Ai depasit limita de maxim ' . $maxEmailsAllowed . ' mesaje trimise / ora. Te rugam sa incerci mai tarziu.');
       }
       
       $data = array('provider_id' => $providerId, 'customer_id' => $customerId, 'product' => $_product);
       Mage::dispatchEvent('contact_request_send_after', $data);
       $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer()  : Mage::getUrl();
       Mage::app()->getFrontController()->getResponse()->setRedirect($url);
       Mage::app()->getResponse()->sendResponse();
       
       return;
	}
		 
	public function replaytomessageAction()
	{
	    if (!$this->getRequest()->getParam('response_message')) {
            $this->_redirect('');
            return;
        }
        
		$replyToId = Mage::getSingleton('customer/session')->getReplyTo();
        $origMessage = Mage::getModel('conturifurnizori/usermessage')->load($replyToId);
        
        $customer = $this->_getSession()->getCustomer();
        //Zend_Debug::dump($customer->getData()); die();
        
        $newMessage = Mage::getModel('conturifurnizori/usermessage')
                               ->setMessage($this->getRequest()->getParam('response_message'))
                               ->setSenderFirstname($customer->getFirstname())
                               ->setSenderLastname($customer->getLastname())
                               ->setReplayTo($customer->getEmail())
                               ->setIsFirst(0)
                               ->setFirstMessageId($replyToId);
                               
        if (strpos($origMessage->getSubject(), 'RE') === false) {
            $newMessage->setSubject('RE: ' . $origMessage->getSubject());
        } else {
            $newMessage->setSubject($origMessage->getSubject());
        }
                               
        if ($customer->getId() == $origMessage->getReceverId()) {
            // reply to sender
            $newMessage->setSenderId($customer->getId());
            $newMessage->setReceverId($origMessage->getSenderId());
        } else {
            // this is a push message
            $newMessage->setSenderId($customer->getId());
            $newMessage->setReceverId($origMessage->getReceverId());
        }
        
        // save new message
        $newMessage->save();
        // Zend_Debug::dump($newMessage); die();
        
        // send transactional email
        $emailSent = $this->_sendNotificationEmail($newMessage, $origMessage);

        if ($emailSent) {
            // add success and redirect
            Mage::getSingleton('core/session')->addSuccess('Raspunsul a fost trimis cu succes');
            $this->_redirect('conturifurnizori/mesaje/casutapostala/');
        }

        return;
	}

    private function _sendNotificationEmail($message, $origMessage = false, $_product = false)
    {
    	//echo 'first'; Zend_Debug::dump($origMessage->getData()); die();
        /*
		array(10) {
		  ["message"] => string(31) "raspuns test la al doilea mesaj"
		  ["sender_firstname"] => string(4) "Bugs"
		  ["sender_lastname"] => string(5) "Bunny"
		  ["replay_to"] => string(28) "bugs.bunny@nuntainimagini.ro"
		  ["is_first"] => int(0)
		  ["first_message_id"] => string(2) "26"
		  ["subject"] => string(55) "RE: Detalii despre oferta Oferta foto-video BB's Agency"
		  ["sender_id"] => string(2) "93"
		  ["recever_id"] => string(2) "94"
		  ["message_id"] => string(2) "38"
		} 
		
        $message = array(9) {
                      ["sender_id"] => string(2) "94"
                      ["recever_id"] => string(2) "93"
                      ["sender_firstname"] => string(4) "Nicu"
                      ["sender_lastname"] => string(5) "Popan"
                      ["subject"] => string(51) "Detalii despre oferta Oferta foto-video BB's Agency"
                      ["message"] => string(20) "Prmiul mesaj de test"
                      ["replay_to"] => string(19) "nicupopan@gmail.com"
                      ["is_first"] => int(1)
                      ["message_id"] => string(2) "15"
                    }
        */
        
        $sender   = $message->getSenderId() ? Mage::getModel('customer/customer')->load($message->getSenderId()) : false;
        $receiver = $message->getReceverId() ? Mage::getModel('customer/customer')->load($message->getReceverId()) : false;
        
        $senderDetails   = array();
        $receiverDetails = array();
        $emailVariables  = array();
        
        if ($sender) {
            if ($sender->getGroupId() == 4) {
                // furnizor
                $senderDetails['sender_fullname'] = ucwords(strtolower($sender->getData('furnizor_contact_firstname') . ' ' . $sender->getData('furnizor_contact_lastname')));
                $senderDetails['sender_email']    = $sender->getData('furnizor_contact_email');
                $senderDetails['sender_phone']    = $sender->getData('furnizor_contact_phone') ? $sender->getData('furnizor_contact_phone') : '';
                $senderDetails['sender_company']  = $sender->getData('business_descriptions_title');
                $subject = 'RE: ' . $message->getSubject(); 
            } else {
                // regular customer => get data from form
                $senderDetails['sender_fullname'] = ucwords(strtolower($message->getData('sender_firstname') . ' ' . $message->getData('sender_lastname')));
                $senderDetails['sender_email']    = $message->getData('replay_to');
                $senderDetails['sender_phone']    = false;
                $subject = $message->getSubject(); 
            }
        } else {
            // regular customer => get data from form
            $senderDetails['sender_fullname'] = ucwords(strtolower($message->getData('sender_firstname') . ' ' . $message->getData('sender_lastname')));
            $senderDetails['sender_email']    = $message->getData('replay_to');
            $senderDetails['sender_phone']    = false;
            $subject = $message->getSubject(); 
        }
        
        if ($receiver) {
            if ($receiver->getGroupId() == 4) {
                // furnizor
                $receiverDetails['receiver_fullname'] = ucwords(strtolower($receiver->getData('furnizor_contact_firstname') . ' ' . $receiver->getData('furnizor_contact_lastname')));
                $receiverDetails['receiver_email']    = $receiver->getData('furnizor_contact_email');
                $receiverDetails['receiver_phone']    = $receiver->getData('furnizor_contact_phone') ? $receiver->getData('furnizor_contact_phone') : false;
                
                $emailTemplate = Mage::getModel('core/email_template')->loadDefault('provider_notification_email');
                $emailSubject = $senderDetails['sender_fullname'] . ' ți-a trimis un mesaje prin nuntainimagini.ro';
            } else {
                // regular customer
                $receiverDetails['receiver_fullname'] = ucwords(strtolower($receiver->getData('firstname') . ' ' . $receiver->getData('lastname')));
                $receiverDetails['receiver_email']    = $receiver->getData('email');
                $receiverDetails['receiver_phone']    = false;
                $emailTemplate = Mage::getModel('core/email_template')->loadDefault('customer_notification_email');
                $emailSubject = $senderDetails['sender_fullname'] . '  de la  ' . $senderDetails['sender_company'] . ' ți-a trimis un mesaje prin NuntaInImagini.ro';
            }
        } else {
            // unregistered regular customer => get data from orig message
            $receiverDetails['receiver_fullname'] = ucwords(strtolower($origMessage->getData('sender_firstname') . ' ' . $origMessage->getData('sender_lastname')));
            $receiverDetails['receiver_email']    = $origMessage->getData('replay_to');
            $receiverDetails['receiver_phone']    = false;
            $emailTemplate = Mage::getModel('core/email_template')->loadDefault('customer_notification_email');
            $emailSubject = $senderDetails['sender_fullname'] . ' de la  ' . $senderDetails['sender_company'] . ' ți-a trimis un mesaje prin NuntaInImagini.ro';
            $emailVariables['phone_entry'] = ' sau la telefon ' . $senderDetails['sender_phone'] . '.';
        }
        
        //Zend_Debug::dump($emailTemplate); die();
        
        // set email template variables
        $emailVariables               = array_merge($emailVariables, $senderDetails);
        $emailVariables               = array_merge($emailVariables, $receiverDetails);
        $emailVariables['created_at'] = date('d.m.Y | H:i:s', Mage::getModel('core/date')->timestamp(time()));
        $emailVariables['store_url']  = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $emailVariables['message']    = $message->getMessage();
        
        $emailVariables['product'] = '';
        
        //Zend_Debug::dump($_product->getData()); die();
        
        if ($_product) {
            $productName = $_product->getName();
            $productUrl = $_product->getProductUrl();
            $productType = $_product->getResource()->getAttribute('ofg_tip_oferta')->getFrontend()->getValue($_product);
            $productFinalPrice = Mage::helper('core')->currency($_product->getFinalPrice(), true, false);
            $regions = '';
            $regionsArr = explode(',', $_product->getOfgZonaPersonalizata());
            foreach ($regionsArr as $region) {
                $regions .= Mage::getModel('directory/region')->load($region)->getName() .', ';
            }
            $productAvailability = trim($regions, ', ');
            $producctFotografi = $_product->getOfgNrFotografi();
            $productCameramani = $_product->getOfgNrCameramani();
            $productTransport = $_product->getResource()->getAttribute('ofg_cheltuieli_transport')->getFrontend()->getValue($_product);
            $productCazare = $_product->getResource()->getAttribute('ofg_cheltuieli_cazare')->getFrontend()->getValue($_product);
            $productTimpAlocat = $_product->getResource()->getAttribute('ofg_disponibilitate')->getFrontend()->getValue($_product);
            
            $emailVariables['product']  = '<p style="font-size:12px; line-height:16px; margin:0 0 16px 0;">Mesajul a fost trimis de pe pagina <a href="' . $productUrl . '">' . $productName . '</a>. ';
            $emailVariables['product'] .= 'Îți transmitem în continuare câteva detalii despre ofertă, așa cum erau la data ' . $emailVariables['created_at'] . ':</p>';
            $emailVariables['product'] .= '<ul style="font-size:12px; line-height:16px; margin:0 0 16px 0; padding:0;">';
            $emailVariables['product'] .= '<li style="list-style:none inside; padding:0 0 0 10px;">&ndash; <strong>Tip oferta</strong>: ' . $productType . '</li>';
            $emailVariables['product'] .= '<li style="list-style:none inside; padding:0 0 0 10px;">&ndash; <strong>Preț / Tarif</strong>: ' . $productFinalPrice . '</li>';
            $emailVariables['product'] .= '<li style="list-style:none inside; padding:0 0 0 10px;">&ndash; <strong>Nr. Fotografi</strong>: ' . $producctFotografi . '</li>';
            $emailVariables['product'] .= '<li style="list-style:none inside; padding:0 0 0 10px;">&ndash; <strong>Nr. Cameramani</strong>: ' . $productCameramani . '</li>';
            $emailVariables['product'] .= '<li style="list-style:none inside; padding:0 0 0 10px;">&ndash; <strong>Valabila pentru</strong>: ' . $productAvailability . '</li>';
            $emailVariables['product'] .= '<li style="list-style:none inside; padding:0 0 0 10px;">&ndash; <strong>Cheltuieli transport</strong>: ' . $productTransport . '</li>';
            $emailVariables['product'] .= '<li style="list-style:none inside; padding:0 0 0 10px;">&ndash; <strong>Cheltuieli cazare</strong>: ' . $productCazare . '</li>';
            $emailVariables['product'] .= '<li style="list-style:none inside; padding:0 0 0 10px;">&ndash; <strong>Timp alocat evenimentului</strong>: ' . $productTimpAlocat . '</li>';
            $emailVariables['product'] .= '</ul>'; 
        }
		
        //load the custom template to the email 
        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_custom1/name', Mage::app()->getStore()->getId()));
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_custom1/email', Mage::app()->getStore()->getId()));
        $emailTemplate->setReplyTo($message->getReplayTo());
        $emailTemplate->setTemplateSubject($emailSubject);
		
		try {
            // send email
            $emailTemplate->send($receiverDetails['receiver_email'], $receiverDetails['receiver_fullname'], $emailVariables);
            return true;
            
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            //Zend_Debug::dump($errorMessage);
            return $errorMessage;
        }
        
    }

	public function getSenderFullName($senderId, $replayTo = false)
    {
        $sender = Mage::getModel('customer/customer')->load($senderId);
        
        if ($sender->getId()) {
            return $sender->getFirstname() . ' ' . $sender->getLastname();
        } else {
            return $replayTo;
        }
        
        return;
    }
    
}









