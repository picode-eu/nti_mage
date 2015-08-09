<?php
class Picode_ProviderReputation_Block_Reputation extends Mage_Core_Block_Template
{
	private $_params;
    private $_providerId;
	private $_customerIp;
    private $_reputation;
    private $_allowedActions = array('profil', 'reputation', 'views', 'contacts');
    
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
		
		$this->_providerId = $providerId;
		$this->_params = $params;
		$this->_customerIp = Mage::helper('core/http')->getRemoteAddr();
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
		$providerReputation = Mage::getModel('providerreputation/reputation')
				->getCollection()
                ->AddFieldToFilter('provider_id', $this->_providerId)
                ->getFirstItem();
		if ($providerReputation->getId()) {
			return $providerReputation;
		}
		
		return '0';
	}
    
    public function getResponse($type)
    {
        $_params = $this->_params;
        $_provider = Mage::getModel('customer/customer')->load($this->_providerId);
        $_customerIp = $this->_customerIp;
        $customerIdentifier = $this->getCustomerIdentifier();
        $session = Mage::getSingleton('customer/session');

        $model = Mage::getModel('providerreputation/' . $type);
        $updateReputation = false;
        
        // update model type excluding reputation table
        if ($type != 'reputation') {
            $values = $model->getCollection()
                ->AddFieldToFilter('provider_id', $this->_providerId)
                ->AddFieldToFilter('customer_identifier', $customerIdentifier)
                ->setOrder('created_at','DESC')
                ->getFirstItem();
				
            if ($values->getId()) {
                $now = Mage::getModel('core/date')->timestamp(time());
                $timeDif = $now - strtotime($values->getCreatedAt());
				
                if ($timeDif > 86400) { // 1 day
                    $updateReputation = true;
                }
            } else {
            	$updateReputation = true;
            }
			
			// new entry
			$model->setProviderId($this->_providerId)
				  ->setCustomerIdentifier($customerIdentifier)
				  ->setCustomerIp($_customerIp)
				  ->save();
        }
		
		// update reputation
		if ($updateReputation) {
			$providerReputation = Mage::getModel('providerreputation/reputation')
				->getCollection()
                ->AddFieldToFilter('provider_id', $this->_providerId)
                ->getFirstItem();
				
			$updateCollumn = 'provider_' . $type;
			$typeValue = $providerReputation->getData($updateCollumn) + 1;
			$providerReputationCount = $providerReputation->getProviderReputation() + 1;
			
			$providerReputation
				->setData($updateCollumn, $typeValue)
				->setData('provider_reputation', $providerReputationCount)
				->save();
		}

        // get updated reputation
        $providerReputation = Mage::getModel('providerreputation/reputation')
            ->getCollection()
            ->AddFieldToFilter('provider_id', $this->_providerId)
            ->getFirstItem();
            
        if ($type == 'reputation') {
            $providerUrl = $this->helper('conturifurnizori')->seoFriendlyUrl($_provider->getBusinessDescriptionsTitle()) . '/' . $this->_providerId;
            
            Mage::dispatchEvent('reputation_updated_after',
                array('provider_reputation' => $providerReputation, 'provider_url' => $this->getBaseUrl() . 'conturifurnizori/furnizori/profil/' . $providerUrl)
            );
        }
            
        if ($providerReputation->getId()) {
            return $providerReputation->getData('provider_' . $type);
        }
        
		return;
    }
    
    public function getContactResponse()
    {
    	$_params = $this->_params;
        $_provider = Mage::getModel('customer/customer')->load($this->_providerId);
		$_customerIp = $this->_customerIp;
        $customerIdentifier = $this->getCustomerIdentifier();
		$updateReputation = false;
        
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
			
            if ($timeDif > 86400) { // 1 day
                $updateReputation = true;
            }
        } else {
        	$updateReputation = true;
        }
        
		// add new contact entry
		$reputationContact = Mage::getModel('providerreputation/contacts')
		      ->setProviderId($this->_providerId)
			  ->setCustomerIdentifier($customerIdentifier)
			  ->setCustomerIp($_customerIp)
			  ->save();
		
		// update reputation
		if ($updateReputation) {
			$reputation = Mage::getModel('providerreputation/reputation')
					->getCollection()
					->AddFieldToFilter('provider_id', $this->_providerId)
	                ->setOrder('created_at','DESC')
	                ->getFirstItem();
					
			$contactsCount = $reputation->getProviderContacts() + 1;
			$providerReputationCount = $reputation->getProviderReputation() + 3;
			
			$reputation
				->setData('provider_contacts', $contactsCount)
				->setData('provider_reputation', $providerReputationCount)
				->save();
		}
        
        if ($output != '') {
            return $output;
        } else {
            return 'Nu exista date de contact.';
        }
    }

    private function getCustomerIdentifier()
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
    
    private function generateUnicKey($string = false)
    {
         $unicId = uniqid() . rand(10, 99);
        
         if ($string) {
              return $string . $unicId;
         } else {
              return $unicId;
         }
    }
}






















