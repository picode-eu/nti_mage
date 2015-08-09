<?php
class Picode_Popularity_Model_Observer
{
	private $_now;
    private $_customerId;
	private $_viewsPoints;
	private $_contactViewsPoints;
	private $_contactsPoints;
    private $_emailsPoints;
	private $_lovesPoints;
	private $_reviewPoints;
	private $_fbSharesPoints;
	private $_gplusShares;
	private $_tweetsPoints;
	
    /**
     * Applies the special params if needed
     */
    public function __construct()
    {
    	$this->_now = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
        $this->_customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $this->_viewsPoints = 1;
		$this->_contactViewsPoints = 2;
		$this->_contactsPoints = 5;
        $this->_emailsPoints = 2;
		$this->_lovesPoints = 3;
		$this->_reviewPoints = 3;
		$this->_gplusShares = 3;
		$this->_fbSharesPoints = 3;
		$this->_tweetsPoints = 3;
    }
    
	/*
	 * update customer id after login
	 * based on ip address
	 * event: customer_login
	 */
    public function updateCustomerId($observer)
    {
        $event = $observer->getEvent();
		
        $_customer = $event->getCustomer();
        $customerIp = Mage::helper('popularity')->getCustomerIpAddress();
        
        $popularityDetails = Mage::getModel('popularity/details')
                ->getCollection()
                ->addFieldToFilter('customer_ip', $customerIp)
                ->addFieldToFilter('customer_id', 0);
                
        if ($popularityDetails->getSize()) {
            foreach ($popularityDetails as $detail) {
                $detail->setCustomerId($_customer->getId());
                $detail->save();
            }
        }
		
		/*
		 * vote for offer
		 * if the customer has chosen to vote an offer before login
		 */
		if (Mage::getSingleton('customer/session')->getVoteOffer()) {
			
			$productId = Mage::getSingleton('customer/session')->getVoteOffer();
            //$_product = Mage::getModel('catalog/product')->load($productId);
			Mage::log('observer after customer logged in ' . $productId .  "\n", false, 'popularity_vote.log');
			$popularityBlock = new Picode_Popularity_Block_Popularity;
			$popularityBlock->addLove($productId);
		}
        
        return $this;
    }
    
	/*
	 * event: catalog_product_load_after
	 */
    public function incrementOfferViews($observer)
    {
    	//Mage::getSingleton('core/session')->setOffersPopularity(); // resets OffersPopularity session var (for debugging mode)
        $event = $observer->getEvent();
        $_product = $event->getProduct();
        //Zend_Debug::dump($_product->getData()); die();
        
        if ($_product->getTypeId() == 'ofertefurnizori' && !Mage::helper('popularity')->botDetected()) {
            
			$popularityType = 'views';
            $popularitySession = unserialize(Mage::getSingleton('core/session')->getOffersPopularity());
            //Zend_Debug::dump($popularitySession);
            
            if (!$popularitySession) {
                $popularitySession = array();
				$popularitySession[$popularityType] = array();
            }
            
            if (!in_array($_product->getId(), $popularitySession[$popularityType])) {
                
                // add general data
                $offerModel = Mage::getModel('popularity/offer');
                
                $offersPopularity = $offerModel->getCollection()
                            ->addFieldToFilter('offer_id', $_product->getId())
                            ->getFirstItem();
                
                
                
                if (!$offersPopularity->getId()) {
                    $offersPopularity = $offerModel->setOfferId($_product->getId());
                }
                
                $views = $offersPopularity->getViews() === null ? 0 : $offersPopularity->getViews();
				$earnedPoints = $offersPopularity->getEarnedPoints() === null ? 0 : $offersPopularity->getEarnedPoints();

				//Zend_Debug::dump($views); die();
                $offersPopularity->setViews($views + 1);
				$offersPopularity->setEarnedPoints($earnedPoints + $this->_viewsPoints);
                $offersPopularity->setUpdatedAt($this->_now);
                $offersPopularity->save();
                
                // increment provider's reputation
                $this->incrementProviderReputation($this->_viewsPoints, $_product->getOfgCustomerId());
				
				// add view details
                $customerId = $this->_customerId ? $this->_customerId : null;
                $customerIp = Mage::helper('popularity')->getCustomerIpAddress();
                
                $popularityDetails = Mage::getModel('popularity/details')
                        ->setData('resource', $offersPopularity->getResourceName())
                        ->setData('popularity_id', $offersPopularity->getId())
                        ->setData('popularity_type', $popularityType)
                        ->setData('customer_id', $customerId)
                        ->setData('customer_ip', $customerIp);
                        
                $popularityDetails->save();
                
                //add the viewed product id to session
                $popularitySession[$popularityType][] =  $_product->getId();
                $serializedOffersPopularity = serialize($popularitySession);
                Mage::getSingleton('core/session')->setOffersPopularity($serializedOffersPopularity);
            }           
        } else {
            Mage::log('bot visit-views ' . Mage::helper('popularity')->getCustomerIpAddress() . "\n" , false, 'bots.log');
        }
        
        $this->updateOfferShares($_product);
        return $this;
    }
    
    /*
     * event: controller_action_postdispatch_sendfriend
     */
    public function incrementEmailsSent($observer)
    {
        $controller = $observer->getControllerAction();
        
        if ($controller->getFullActionName() == 'sendfriend_product_sendmail') {
            
            $_product = Mage::getModel('catalog/product')->load($controller->getRequest()->getParam('id'));
            
            if ($_product->getTypeId() == 'ofertefurnizori' && !Mage::helper('popularity')->botDetected()) {
                $popularityType = 'emails';
                
                // add general data
                $offerModel = Mage::getModel('popularity/offer');
                
                $offersPopularity = $offerModel->getCollection()
                            ->addFieldToFilter('offer_id', $_product->getId())
                            ->getFirstItem();
                
                if (!$offersPopularity->getId()) {
                    $offersPopularity = $offerModel->setOfferId($_product->getId());
                }
                
                //Zend_Debug::dump($offersPopularity->getData());
                
                $emails = $offersPopularity->getEmailsSent() === null ? 0 : $offersPopularity->getEmailsSent();
                $earnedPoints = $offersPopularity->getEarnedPoints() === null ? 0 : $offersPopularity->getEarnedPoints();
                
                $recipients = $controller->getRequest()->getParam('recipients');
                $recipientsCount = count($recipients['email']);
                
                $earnedPoints += $this->_emailsPoints * $recipientsCount;
                
                $offersPopularity->setEmailsSent($emails + $recipientsCount);
                $offersPopularity->setEarnedPoints($earnedPoints);
                $offersPopularity->setUpdatedAt($this->_now);
                $offersPopularity->save();
                
                // add view details
                $customerId = $this->_customerId ? $this->_customerId : null;
                $customerIp = Mage::helper('popularity')->getCustomerIpAddress();
                
                $popularityDetails = Mage::getModel('popularity/details')
                        ->setData('resource', $offersPopularity->getResourceName())
                        ->setData('popularity_id', $offersPopularity->getId())
                        ->setData('popularity_type', $popularityType)
                        ->setData('customer_id', $customerId)
                        ->setData('customer_ip', $customerIp);
                        
                $popularityDetails->save();
                
                // increment provider's reputation
                $this->incrementProviderReputation($this->_emailsPoints, $_product->getOfgCustomerId());
                
            } else {
                Mage::log('bot visit-email ' . Mage::helper('popularity')->getCustomerIpAddress() . "\n" , false, 'bots.log');
            }
            
        }
        
        return $this;
    }

    public function updateOfferShares($_product)
    {
        if ($_product->getTypeId() == 'ofertefurnizori' && !Mage::helper('popularity')->botDetected()) {
            $url = Mage::getBaseUrl() . $_product->getUrlPath();
            $helper = Mage::helper('popularity');
            
            $OffersPopularity = Mage::getModel('popularity/offer')
                    ->getCollection()
                    ->addFieldToFilter('offer_id', $_product->getId())
                    ->getFirstItem();
                    
            $earnedPoints = $OffersPopularity->getEarnedPoints();
            $newEarnedPoints = 0;
            
            // update Facebook Shares
            $newFbShares = $helper->getFb($url);
            $fbShares = $OffersPopularity->getFbShares();
            
            if ($newFbShares != $fbShares) {
                $dif = $newFbShares - $fbShares;
                $newEarnedPoints = $dif * $this->_fbSharesPoints;
                $earnedPoints = $earnedPoints + $newEarnedPoints;
                
                $OffersPopularity->setFbShares($newFbShares);
                $OffersPopularity->setEarnedPoints($earnedPoints);
            }
            
            // update Google+ Shares
            $newGplusShares = $helper->getPlusones($url);
            $gplusShares = $OffersPopularity->getGplusShares();
            
            if ($newGplusShares != $gplusShares) {
                $dif = $newGplusShares - $gplusShares;
                $newEarnedPoints = $dif * $this->_gplusShares;
                $earnedPoints = $earnedPoints + $newEarnedPoints;
                
                $OffersPopularity->setGplusShares($newGplusShares);
                $OffersPopularity->setEarnedPoints($earnedPoints);
            }
            
            // update Tweets
            $newTweets = $helper->getTweets($url);
            $tweets = $OffersPopularity->getTweets();
            
            if ($newTweets != $tweets) {
                $dif = $newTweets - $tweets;
                $newEarnedPoints = $dif * $this->_tweetsPoints;
                $earnedPoints = $earnedPoints + $newEarnedPoints;
                
                $OffersPopularity->setTweets($newTweets);
                $OffersPopularity->setEarnedPoints($earnedPoints);
            }
            
            $OffersPopularity->save();
            
            // increment provider's reputation
            $this->incrementProviderReputation($newEarnedPoints, $_product->getOfgCustomerId());
        }
        
        return;
    }

    /*
	 * update earned points for ratings and comments added
	 * applied only for approved reviews
	 * event: review_save_after (admin)
	 */
    public function updatePointsOnReviewApproved($observer)
    {
        $data = $observer->getEvent()->getObject()->getData();
        $_product = Mage::getModel('catalog/product')->load($data['entity_pk_value']);
        
        if ($_product->getTypeId() == 'ofertefurnizori' && $data['status_id'] == 1) {
            
            $popularityType = 'reviews';
            
            // add general data
            $offerModel = Mage::getModel('popularity/offer');
            
            $offersPopularity = $offerModel->getCollection()
                        ->addFieldToFilter('offer_id', $_product->getId())
                        ->getFirstItem();
            
            if (!$offersPopularity->getId()) {
                $offersPopularity = $offerModel->setOfferId($_product->getId());
            }
            
            $comments = $offersPopularity->getComments() === null ? 0 : $offersPopularity->getComments();
            $earnedPoints = $offersPopularity->getEarnedPoints() === null ? 0 : $offersPopularity->getEarnedPoints();
            
            $offersPopularity->setComments($comments + 1);
            $offersPopularity->setEarnedPoints($earnedPoints + $this->_reviewPoints);
            $offersPopularity->setUpdatedAt($this->_now);
            $offersPopularity->save();
            
            // add view details
            $customerId = $data['customer_id'] ? $data['customer_id'] : null;
            $customerIp = Mage::helper('popularity')->getCustomerIpAddress();
            
            $popularityDetails = Mage::getModel('popularity/details')
                    ->setData('resource', $offersPopularity->getResourceName())
                    ->setData('popularity_id', $offersPopularity->getId())
                    ->setData('popularity_type', $popularityType)
                    ->setData('customer_id', $customerId)
                    ->setData('customer_ip', $customerIp)
                    ->save();
            
            // increment provider's reputation
            $this->incrementProviderReputation($this->_reviewPoints, $_product->getOfgCustomerId());
            
        }
        
        return $this;        
    }
    
    /*
     * event: update_contact_sent
     */
    public function incrementContactSent($observer)
    {
        //Mage::getSingleton('core/session')->setOffersPopularity(); // resets OffersPopularity session var (for debugging mode)
        Mage::log('incrementContactSent', false, 'update_contact.log');
        $event = $observer->getEvent();
        $providerId = $event->getProviderId();
        $customerId = $event->getCustomerId();
        $_product = $event->getProduct();
        
        if ($providerId == $this->_customerId) {
            // do nothing
            return $this;
        }
        
        if ($_product->getTypeId() == 'ofertefurnizori' && !Mage::helper('popularity')->botDetected()) {
            
            $popularityType = 'contact_sent';
            $popularitySession = unserialize(Mage::getSingleton('core/session')->getOffersPopularity());
            //Zend_Debug::dump($popularitySession);
            
            if (!isset($popularitySession[$popularityType])) {
                $popularitySession[$popularityType] = array();
            }
            
            if (!in_array($_product->getId(), $popularitySession[$popularityType])) {
                
                // add general data
                Mage::log('incrementContactSent register new ' . $providerId . ' | ' . $customerId . ' | ' . $_product->getId(), false, 'update_contact.log');
                $offerModel = Mage::getModel('popularity/offer');
                
                $offersPopularity = $offerModel->getCollection()
                            ->addFieldToFilter('offer_id', $_product->getId())
                            ->getFirstItem();
                
                if (!$offersPopularity->getId()) {
                    // something is not ok => do nothing
                    return $this;
                }
                
                $contactSent = $offersPopularity->getContacts() === null ? 0 : $offersPopularity->getContacts();
                $earnedPoints = $offersPopularity->getEarnedPoints() === null ? 0 : $offersPopularity->getEarnedPoints();

                $offersPopularity->setContacts($contactSent + 1);
                $offersPopularity->setEarnedPoints($earnedPoints + $this->_contactsPoints);
                $offersPopularity->setUpdatedAt($this->_now);
                $offersPopularity->save();
                
                // add view details
                $customerId = $customerId ? $customerId : null;
                $customerIp = Mage::helper('popularity')->getCustomerIpAddress();
                
                $popularityDetails = Mage::getModel('popularity/details')
                        ->setData('resource', $offersPopularity->getResourceName())
                        ->setData('popularity_id', $offersPopularity->getId())
                        ->setData('popularity_type', $popularityType)
                        ->setData('customer_id', $customerId)
                        ->setData('customer_ip', $customerIp);
                        
                $popularityDetails->save();
                
                // increment provider's reputation
                $this->incrementProviderReputation($this->_contactsPoints, $_product->getOfgCustomerId());
                
                //add the viewed product id to session
                $popularitySession[$popularityType][] =  $_product->getId();
                $serializedOffersPopularity = serialize($popularitySession);
                Mage::getSingleton('core/session')->setOffersPopularity($serializedOffersPopularity);
            }           
        } else {
            Mage::log('bot visit-views ' . Mage::helper('popularity')->getCustomerIpAddress(), false, 'bots.log');
        }
        
        return $this;
    }

    public function incrementProviderReputation($points, $providerId)
    {
        $providerReputation = Mage::getModel('providerreputation/reputation')
                ->getCollection()
                ->addFieldToFilter('provider_id', $providerId)
                ->getFirstItem();
                
        // Zend_Debug::dump($providerReputation->getData());
        // return;
                
        $providerReputation->setProviderReputation($providerReputation->getProviderReputation() + $points);
        $providerReputation->save();
        
        $provider = Mage::getModel('customer/customer')->load($providerId)
            ->setProviderReputation($providerReputation->getProviderReputation())
            ->save();
        
        return;                
    }
    
	/*
	 * get user's ip address
	 */
    // private function _getCustomerIpAddress()
    // {
         // if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
             // $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
         // } else {
             // $ip = $_SERVER['REMOTE_ADDR'];
         // }
//         
         // $is_local_network = false;
//         
         // if (
              // (strpos($ip,'192.168') === 0)||
              // (strpos($ip,'10.') === 0)      ||
              // (strpos($ip,'172.16') === 0) ||
              // (strpos($ip,'172.31') === 0) ||
              // (strpos($ip,'172.0.0.1') === 0)
            // )
         // {
              // $is_local_network = true;
         // }
//         
         // if ($is_local_network) {
              // $ips = dns_get_record($_SERVER['SERVER_NAME']);
//              
              // foreach ($ips as $item) {
                   // if(isset($item['type']))
                        // if($item['type']=='A')
                             // if(isset($item['ip'])){
                                  // return $item['ip'];
                                  // break;
                             // }
              // }
// 
         // } else {
              // return $ip;
         // }
//          
         // return null;
    // }

    // function _botDetected()
    // {
        // $ip = Mage::helper('popularity')->getCustomerIpAddress();
        // $blockedIps = array('23.253.162.123', '74.112.131.244', '23.96.208.137', '150.70.97.122', '64.13.133.149', '64.13.133.152', '66.249.92.8'); 
//         
        // if (in_array($ip, $blockedIps)) {
            // return true;
        // } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
            // return true;
        // } else {
            // return false;
        // }
//     
    // }
        
    
}
