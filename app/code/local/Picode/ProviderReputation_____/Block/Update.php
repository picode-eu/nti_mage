<?php

class Picode_ProviderReputation_Block_Update extends Mage_Core_Block_Template
{
    private $_params;
    private $_providerId;
	private $_customerIp;
    private $_customerIdentifier;
	private $_reputationTypes = array(
					'reputation',
					'views',
					'facebook',
					'gplus',
					'tweets',
	               );
                   
    private $_allowedActions = array(
                    'profil',
                    'reputation',
                    'contacts',
                    'loves',
                    'sendemail'
                   );
                   
    private $_now;
	
	public function __construct()
    {
        $action = $this->getRequest()->getActionName();
        $params = $this->getRequest()->getParams();
        
        if (in_array($action, $this->_allowedActions)) {
            foreach ($params as $key => $val) {
                if ($key == 'provider_id' && is_numeric($val)) {
                    $providerId = (int) $val;
                } elseif ($action = 'profil' && is_numeric($val)) {
                    $providerId = (int) $val;
                } else {
                    $providerId = false;
                    unset($key);
                }
            }
        } else {
            $this->_providerId = false;
        }
        
        $this->_params = $params;
        $this->_providerId = $providerId;
		$this->_customerIp = Mage::helper('core/http')->getRemoteAddr();
        $this->_customerIdentifier = $this->_getCustomerIdentifier();
        $this->_now = date("Y-m-d H:i:s",  strtotime(Mage::getModel('core/date')->date('Y-m-d H:i:s') . ' -1 hour'));
    }
    
    public function getLoadedProviderId()
    {
        return $this->_providerId;
    }
    
    public function getLoadedProviderDetails()
    {
        return Mage::getModel('customer/customer')->load($this->getLoadedProviderId());
    }
    
    public function getProviderReputation()
    {
        $providerReputation = $this->_getModelType('reputation')
                ->getCollection()
                ->AddFieldToFilter('provider_id', $this->_providerId)
                ->getFirstItem();
        if ($providerReputation->getId()) {
            return $providerReputation;
        }
        
        return '0';
    }
	
	public function updateReputation()
	{
		if ($this->_botDetected()) {
			return;
		}
		
		$_provider = Mage::getModel('customer/customer')->load($this->_providerId);
		$customerIdentifier = $this->_getCustomerIdentifier();
		$updateReputation = false;
		$updateReputationColumn = array();
		$responseArr = array();
        (int)$updateValue = 0;
        
        $_helper = Mage::helper('conturifurnizori');
        $providerUrl = $this->getBaseUrl() . 'conturifurnizori/furnizori/profil/' . $_helper->seoFriendlyUrl($_provider->getBusinessDescriptionsTitle()) . '/' . $_provider->getId();

        
	    // check if is set parameter "type" and change it accordingly
		$type = $this->getRequest()->getParam('type') ? $this->getRequest()->getParam('type') : 'reputation';
        $column = 'provider_' . $type;
        
        // get and update real social shares
        if ($type == 'views') {
            $this->_updateSocials($this->_providerId, $providerUrl);
        }
        
        // load reputation type by request
        $reputationType = $this->_getModelType($type)
                ->getCollection()
                ->AddFieldToFilter('provider_id', $this->_providerId)
                ->AddFieldToFilter('customer_identifier', $customerIdentifier)
                ->setOrder('created_at','DESC')
                ->getFirstItem();
        
	    switch ($type) {
			case 'facebook':
            case 'gplus':
            case 'tweets':
				// get and update current social share
				if ($reputationType->getId()) {
                    $now = Mage::getModel('core/date')->timestamp(time());
                    $timeDif = $now - strtotime($reputationType->getCreatedAt());
                    (int)$updateValue = 3;
                    
                    if ($timeDif > 10) { // 3 hours = 10800 sec
                        $updateReputation = true;
                        $updateReputationColumn[] = $column;
                    }
                } else {
                    $updateReputation = true;
                    $updateReputationColumn[] = $column;
                }
                
				// add new entry
                $reputationType = $this->_getModelType($type)
                      ->setProviderId($this->_providerId)
                      ->setCustomerIdentifier($customerIdentifier)
                      ->setCustomerIp($this->_customerIp)
                      ->save();
					  
				break;
                
            case 'views';
                // check and update current view
                // if ($updateReputation = $this->_isUpdateAllowed($reputationType, $column, 10, 3)) { // 1 day = 86400 sec
                    // $updateReputationColumn[] = $column;
				// }
                
                if ($reputationType->getId()) {
                    $now = Mage::getModel('core/date')->timestamp(time());
                    $timeDif = $now - strtotime($reputationType->getCreatedAt());
                    
                    (int)$updateValue = 1;
                    
                    if ($timeDif > 10) { // 1 day = 86400 sec
                        $updateReputation = true;
                        $updateReputationColumn[] = $column;
                    }
                } else {
                    $updateReputation = true;
                    $updateReputationColumn[] = $column;
                }
                
                // add new entry
                $reputationType = $this->_getModelType($type)
                      ->setProviderId($this->_providerId)
                      ->setCustomerIdentifier($customerIdentifier)
                      ->setCustomerIp($this->_customerIp)
                      ->save();
                
                break;
		}

        // update ruputation table
        $reputation = $this->_getModelType('reputation')
            ->getCollection()
            ->AddFieldToFilter('provider_id', $this->_providerId)
            ->getFirstItem();
            
        if ($updateReputation) {
            
            foreach ($updateReputationColumn as $column) {
                $columnCount = $reputation->getData($column) + 1;
                $reputation->setData($column, $columnCount);
                
                $reputationCount = $reputation->getProviderReputation() + $updateValue;
                $reputation->setData('provider_reputation', $reputationCount);
            }
			
            //$responseArr['debug1'] = $updateReputation ? '1 + ' . $updateValue : '0';
            
            $reputation->save();
            $this->updateProviderDetails($reputation->getData($column), $_provider, $column);
            $this->updateProviderDetails($reputation->getData('provider_reputation'), $_provider);
        }
        
        foreach ($this->_reputationTypes as $type) {
            if ($type == 'reputation')
                $responseArr['provider-reputation'] = Mage::helper('conturifurnizori')->convertReputationPoints($reputation->getData('provider_reputation'));
            else 
                $responseArr['provider-' . $type] = $reputation->getData('provider_' . $type);
        }
        
		//Mage::log($responseArr, false, 'reputation_debug.log');
		return json_encode($responseArr);
	}

	// private function _isUpdateAllowed($reputationType, $column, $maxDiff, $updateValue = 1)
	// {
		// $type = explode('_', $column);
		// $update = false;
// 		
		// if ($reputationType->getId()) {
            // $now = Mage::getModel('core/date')->timestamp(time());
            // $timeDif = $now - strtotime($reputationType->getCreatedAt());
            // $updateValue = 3;
//             
            // if ($timeDif > $maxDiff) { // 3 hours = 10800 sec
                // $update = true;
            // }
// 			
        // } else {
            // $update = true;
        // }
//         
		// // add new entry
        // $reputationType = $this->_getModelType(end($type))
              // ->setProviderId($this->_providerId)
              // ->setCustomerIdentifier($this->_customerIdentifier)
              // ->setCustomerIp($this->_customerIp)
              // ->save();
// 			  
		// return $update;
	// }

    private function _getModelType($type)
    {
        $type = 'providerreputation/' . $type;
        return Mage::getModel($type);
    }
    
    private function getUpdatedReputation()
    {
        $reputation = $this->_getModelType('reputation')
            ->getCollection()
            ->AddFieldToFilter('provider_id', $this->_providerId)
            ->getFirstItem();
            
        return $reputation;
    }
	
	private function _getCustomerIdentifier()
    {
        $cookie = Mage::getSingleton('core/cookie');
        $session = Mage::getSingleton('customer/session');
        
        if ($cookie->get('cstidf')) {
            return $cookie->get('cstidf');
        } elseif ($session->getData('cstidf')) {
            return $session->getData('cstidf');
        }
        
        return false;
    }
    
    private function _updateSocials($providerId, $providerUrl)
    {
        $_helper = Mage::helper('providerreputation');
        (int)$updateValue = 3;
        
        $providerReputation = $this->_getModelType('reputation')
            ->getCollection()
            ->AddFieldToFilter('provider_id', $providerId)
            ->getFirstItem();
        
        // update facebook
        $facebookShare = $_helper->getSocialShare('facebook', $providerUrl);
        $newReputationValue = $this->_calculateNewReputation('provider_facebook', $providerReputation, $facebookShare, $updateValue);
        $reputationValue = $providerReputation->getProviderReputation() + $newReputationValue;

        $providerReputation->setData('provider_facebook', $facebookShare);
        $providerReputation->setData('provider_reputation', $reputationValue);
        
        // update gplus
        $gplusShare  = $_helper->getSocialShare('gplus', $providerUrl);
        $newReputationValue = $this->_calculateNewReputation('provider_gplus', $providerReputation, $gplusShare, $updateValue);
        $reputationValue = $providerReputation->getProviderReputation() + $newReputationValue;
        
        $providerReputation->setData('provider_gplus', $gplusShare);
        $providerReputation->setData('provider_reputation', $reputationValue);
        
        // update tweets
        $tweetsShare = $_helper->getSocialShare('tweets', $providerUrl);
        $newReputationValue = $this->_calculateNewReputation('provider_tweets', $providerReputation, $tweetsShare, $updateValue);
        $reputationValue = $providerReputation->getProviderReputation() + $newReputationValue;
        
        $providerReputation->setData('provider_tweets', $tweetsShare);
        $providerReputation->setData('provider_reputation', $reputationValue);
        
        $providerReputation->save();
        
        return;
    }
    
    private function _calculateNewReputation($column, $providerReputation, $socialShareCount, $updateValue)
    {
        $actualColumnCount = $providerReputation->getData($column);
        $diffSocialCount = $socialShareCount - $actualColumnCount;
        $newReputationValue = ($diffSocialCount * $updateValue);
        
        return $newReputationValue;
    }
	
	private function _botDetected()
    {
        $ip = $this->getCustomerIpAddress();
        $blockedIps = array(); 
        //$blockedIps = array('23.253.162.123', '74.112.131.244', '23.96.208.137', '150.70.97.122', '64.13.133.149', '64.13.133.152', '66.249.92.8', '216.46.175.35', '216.46.190.190', '74.112.131.242', '150.70.173.43', '74.112.131.242', '66.249.83.60', '66.249.88.205', '66.102.6.98');
        
        if (in_array($ip, $blockedIps)) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getContactResponse()
    {
        $_provider = Mage::getModel('customer/customer')->load($this->_providerId);
        $_customerIp = $this->_customerIp;
        $customerIdentifier = $this->_getCustomerIdentifier();
        $updateReputation = false;
        $responseArr = array();
        
        // start building output data
        $output = '';
        
        if ($_provider->getFurnizorContactFirstname() || $_provider->getFurnizorContactLastname()) {
            $output .= '<li class="contact-name">';
                $output .= '<span class="ion-person"></span>';
                $output .= $_provider->getResource()->getAttribute('furnizor_contact_firstname')->getFrontend()->getValue($_provider);
                $output .= ' ';
                $output .= $_provider->getResource()->getAttribute('furnizor_contact_lastname')->getFrontend()->getValue($_provider);
            $output .= '</li>';
        }
        
        if ($_provider->getFurnizorContactEmail()) {
            
            $output .= '<li class="contact-email">';
                $output .= '<span class="ion-at"></span>';
                $output .= $_provider->getFurnizorContactEmail();
            $output .= '</li>';
        }
        
        if ($_provider->getFurnizorContactPhone()) {
            
            $output .= '<li class="contact-phone">';
                $output .= '<span class="ion-ios-telephone"></span>';
                $output .= $_provider->getResource()->getAttribute('furnizor_contact_phone')->getFrontend()->getValue($_provider);
            $output .= '</li>';
        }
        
        if ($_provider->getBusinessNetworksSkype()) {
            
            $output .= '<li class="contact-phone">';
                $output .= '<span class="ion-social-skype"></span>';
                $output .= $_provider->getResource()->getAttribute('business_networks_skype')->getFrontend()->getValue($_provider);
            $output .= '</li>';
        }
        
        // update reputation contact count
        $reputationContact = Mage::getModel('providerreputation/contacts')
                ->getCollection()
                ->AddFieldToFilter('provider_id', $this->_providerId)
                ->AddFieldToFilter('customer_identifier', $customerIdentifier)
                ->setOrder('created_at','DESC')
                ->getFirstItem();
                
        if ($reputationContact->getId()) {
            $now = Mage::getModel('core/date')->timestamp(time());
            $timeDif = $now - strtotime($reputationContact->getCreatedAt());
            
            if ($timeDif > 10) { // 1 day = 86400 sec
                $updateReputation = true;
            }
        } else {
            $updateReputation = true;
        }
        
        // add new contact entry
        $reputationContact = $this->_getModelType('contacts')
              ->setProviderId($this->_providerId)
              ->setCustomerIdentifier($customerIdentifier)
              ->setCustomerIp($_customerIp)
              ->save();
        
        // update reputation
        if ($updateReputation) {
            $reputation = $this->_getModelType('reputation')
                    ->getCollection()
                    ->AddFieldToFilter('provider_id', $this->_providerId)
                    ->setOrder('created_at','DESC')
                    ->getFirstItem();
                    
            $contactsCount = $reputation->getProviderContacts() + 1;
            $providerReputationCount = $reputation->getProviderReputation() + 1;
            
            $reputation
                ->setData('provider_contacts', $contactsCount)
                ->setData('provider_reputation', $providerReputationCount)
                ->save();
                
            $this->updateProviderDetails($reputation->getProviderReputation(), $_provider);
        }
        
        if ($output != '') {
            $responseArr['contact-response'] = $output;
            $responseArr['provider-reputation'] = Mage::helper('conturifurnizori')->convertReputationPoints($reputation->getProviderReputation());
        } else {
            $responseArr['contact-response'] = 'Nu exista date de contact.';
        }
        
        return json_encode($responseArr);
    }

    public function getLoveResponse()
    {
        $_provider = Mage::getModel('customer/customer')->load($this->_providerId);
        $reputation = $this->_getModelType('reputation')
            ->getCollection()
            ->AddFieldToFilter('provider_id', $this->_providerId)
            ->setOrder('created_at','DESC')
            ->getFirstItem();
            
        if ($this->canLove()) {
            $lovePoints = 2;
            
            // update loves table
            $loves = $this->_getModelType('loves')
                ->setProviderId($this->_providerId)
                ->setCustomerIdentifier($this->_customerIdentifier)
                ->setCustomerIp($this->_customerIp)
                ->save();
                
            // update reputation table
            $reputation->setProviderLoves($reputation->getProviderLoves() + 1);
            $reputation->setProviderReputation($reputation->getProviderReputation() + $lovePoints);
            $reputation->save();
            
        } else {
            $responseArr['response'] = 'Ai votat deja pentru acest furnizor. Voturile multiple nu sunt acceptate.';
        }

        $updatedReputation = $this->getUpdatedReputation();
        
        $responseArr['provider-reputation'] = Mage::helper('conturifurnizori')->convertReputationPoints($updatedReputation->getProviderReputation());
        $responseArr['provider-loves'] = $updatedReputation->getProviderLoves();
        
        return json_encode($responseArr);
    }

    public function canLove()
    {
        $loves = $this->_getModelType('loves')->getCollection()
                    ->AddFieldToFilter('provider_id', $this->_providerId)
                    ->AddFieldToFilter('customer_identifier', $this->_customerIdentifier)
                    ->setOrder('created_at','DESC')
                    ->getFirstItem();
        
        
        if ($loves->getId()) {
            return false;
        }
        
        return true;
    }
    
    public function getSendemailResponse()
    {
        $maxEmails = 500;
        
        if ($this->canSendNewEmail($maxEmails)) {
            $senderDetails   = array();
            $receiverDetails = array();
            $emailVariables  = array();
            $updateReputation = false;
            
            $emailTemplate = Mage::getModel('core/email_template')->loadDefault('send_to_friend');
            // email details
            $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_custom2/name', Mage::app()->getStore()->getId()));
            $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_custom2/email', Mage::app()->getStore()->getId()));
            
            // sender details
            $_sender = Mage::getSingleton('customer/session')->getCustomer();
            $senderDetails['sender_fullname'] = ucwords($this->_params['sender_fullname']);
            $senderDetails['sender_ip'] =  $this->_customerIp;
            
            $_sender = Mage::getSingleton('customer/session');
            if($_sender->isLoggedIn()) {
                //Zend_Debug::dump($_sender->getData()); die();
                $_sender = Mage::getModel('customer/customer')->load($_sender->getId());
                //$senderDetails['sender_email'] = $_sender->getEmail();
                $emailTemplate->setReplyTo($_sender->getEmail());
            }
            
            $emailSubject = $senderDetails['sender_fullname'] . ' ți-a trimis un link prin ' . Mage::getStoreConfig('trans_email/ident_custom2/name', Mage::app()->getStore()->getId());
            $emailTemplate->setTemplateSubject($emailSubject);
            
            // receiver details
            $receiverDetails['receiver_email'] = $this->_params['receiver_email'];
            
            // set email variables
            $_provider = Mage::getModel('customer/customer')->load($this->_providerId);
            
            $emailVariables['created_at'] = $this->_now;
            $emailVariables['provider_services'] = strtolower($_provider->getResource()->getAttribute('furnizor_company_services')->getFrontend()->getValue($_provider));
            $emailVariables['provider_name'] = $_provider->getBusinessDescriptionsTitle();
            $emailVariables['provider_details']  = '<li style="float:left; width:200px; list-style: inside none none; margin:5px 0 0 0; padding: 0 10px 0 0;">';
            $emailVariables['provider_details'] .= '<a href="' . $this->getBaseUrl() . 'conturifurnizori/furnizori/profil/' . Mage::helper('conturifurnizori')->seoFriendlyUrl($_provider->getBusinessDescriptionsTitle()) . '/' . $_provider->getId() . '">';
            $emailVariables['provider_details'] .= '<img style="width: 100%; margin:0; padding:0;" src="' . Mage::helper('conturifurnizori')->resizeImage($_provider->getBusinessImagesLogo(), '200', false, true) . '" alt="' . $_provider->getBusinessDescriptionsTitle() . '" />';
            $emailVariables['provider_details'] .= '</a>';
            $emailVariables['provider_details'] .= '</li>';
            $emailVariables['provider_details']  .= '<li style="width: auto; list-style: inside none none; padding: 0px; margin: 2px 0;"><strong>Denumire:</strong> ' . $_provider->getBusinessDescriptionsTitle() . '</strong>';
            $emailVariables['provider_details']  .= '<li style="width: auto; list-style: inside none none; padding: 0px; margin:2px 0;"><strong>Servicii:</strong> ' . $_provider->getResource()->getAttribute('furnizor_company_services')->getFrontend()->getValue($_provider) . '</li>';
            $emailVariables['provider_details']  .= '<li style="width: auto; list-style: inside none none; padding: 0px; margin:2px 0;"><strong>Sediul:</strong> ' . $_provider->getFurnizorLocationCity() . ', ' . $_provider->getResource()->getAttribute('furnizor_location_province')->getFrontend()->getValue($_provider) . '</li>';
            $emailVariables['provider_details']  .= '<li style="width: auto; list-style: inside none none; padding: 0px; margin:2px 0;"><strong>Experiență:</strong>: ' . $_provider->getResource()->getAttribute('business_descriptions_exp')->getFrontend()->getValue($_provider) . '</li>';
            $emailVariables['provider_details'] .= '<li style="width: auto; list-style: inside none none; padding: 0px; margin:2px 0;"><strong>Descriere:</strong> ' . $_provider->getBusinessDescriptionsDesc() . '</li>';
            $emailVariables['provider_url'] = $this->getBaseUrl() . 'conturifurnizori/furnizori/profil/' . Mage::helper('conturifurnizori')->seoFriendlyUrl($_provider->getBusinessDescriptionsTitle()) . '/' . $_provider->getId();
            $emailVariables['store_url']  = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
            $emailVariables['message']    = $this->_params['message'];
    
            $emailVariables = array_merge($emailVariables, $senderDetails);
            $emailVariables = array_merge($emailVariables, $receiverDetails);
            
            // add new sentemails entry
            $emailsentReputation = $this->_getModelType('emails')
                  ->setProviderId($this->_providerId)
                  ->setCustomerIdentifier($this->_customerIdentifier)
                  ->setCustomerIp($this->_customerIp)
                  ->setReceiverEmail($receiverDetails['receiver_email'])
                  ->save();
                  
            // update reputation
            $reputation = $this->_getModelType('reputation')
                    ->getCollection()
                    ->AddFieldToFilter('provider_id', $this->_providerId)
                    ->setOrder('created_at','DESC')
                    ->getFirstItem();
                    
            $emailsCount = $reputation->getProviderEmails() + 1;
            $providerReputationCount = $reputation->getProviderReputation() + 2;
            
            $reputation
                ->setData('provider_emails', $emailsCount)
                ->setData('provider_reputation', $providerReputationCount)
                ->save();
                
            $this->updateProviderDetails($reputation->getProviderReputation(), $_provider);
            
            try {
                // send email
                $emailTemplate->send($receiverDetails['receiver_email'], '', $emailVariables);

                // set response
                $responseArr['response'] = $this->__('Mesajul a fost trimis cu succes către: %s', $this->_params['receiver_email']);
                $responseArr['provider-reputation'] = Mage::helper('conturifurnizori')->convertReputationPoints($reputation->getProviderReputation());
                $responseArr['provider-emails'] = $reputation->getProviderEmails();
    
                return json_encode($responseArr);
                
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
                //$responseArr['send-response'] = $errorMessage;
    
                return json_encode($responseArr);
            }
            
        } else {
            $responseArr['send-response'] = $this->__('Ai depășit limita de maxim %s mesaje email pe oră. Te rugăm să încerci mai târziu.', $maxEmails);
            return json_encode($responseArr);
        }
        
        return;
    }

    public function canSendNewEmail($maxEmails)
    {
        $emails = Mage::getModel('providerreputation/emails')
            ->getCollection()
            ->AddFieldToFilter('customer_identifier', $this->_customerIdentifier);
            
        if ($emails->getSize() >= $maxEmails) {
            $minLastUpdate = date("Y-m-d H:i:s",  strtotime($this->_now . ' -1 hour'));
            $emailsCount = 0;
            
            foreach ($emails as $email) {
                
                if ($emailsCount >= $maxEmails) {
                    return false;
                } else {
                    if (strtotime($email->getCreatedAt()) > strtotime($minLastUpdate)) {
                        $emailsCount++;
                    }
                }
                
            }
        }

        return true;
    }

    private function updateProviderDetails($value, $_provider = false, $column = 'provider_reputation')
    {
        if (!$_provider) {
            $_provider = Mage::getModel('customer/customer')->load($this->_providerId);
        }
        
        $_provider->setData($column, $value);
        $_provider->save();
        
        return;
    }


    
}
