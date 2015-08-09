<?php
class Picode_Popularity_Block_Popularity extends Mage_Core_Block_Template
{
    private $_now;
    private $_customerId;
    private $_contactViewsPoints;
    private $_lovesPoints;
    
    /**
     * Applies the special params if needed
     */
    public function __construct()
    {
        $this->_now = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
        $this->_customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $this->_contactViewsPoints = 2;
        $this->_lovesPoints = 5;
    }
    
	public function addLove($productId = false)
    {
        $response = 'error';
		
		/*
		 * redirect customer with notice message if is not logged in
		 */
		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
			Mage::getSingleton('customer/session')->setVoteOffer($this->getRequest()->getParam('product'));
            
            $loginUrl = Mage::getBaseUrl() . 'customer/account/login';
            $creatUrl = Mage::getBaseUrl() . 'customer/account/create';
            $message = 'Autentifică-te în <a href="' . $loginUrl . '">contul tău</a> pentru a vota. Dacă încă nu ai un cont <a href="' . $creatUrl . '">crează-ţi</a> unul chiar acum.';
            
			Mage::getSingleton('core/session')->addNotice($message);
            
			return $response;
		}
        
		if (!$productId) {
			$productId = $this->getRequest()->getParam('product');
		}
		
        if ($productId) {
            $_product = Mage::getModel('catalog/product')->load($productId);
            
            if ($_product) {
                // get/set some current data
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $now = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
                $popularityType = 'loves';
				
                $popularitySession = unserialize(Mage::getSingleton('core/session')->getOffersPopularity());
                if (!isset($popularitySession[$popularityType])) {
                    $popularitySession[$popularityType] = array();
                }
                
                /*
                 * check if the current customer has loved this product before
                 * based on his ip address and/or customer id
                 */
                $customerIp = $this->helper('popularity')->getCustomerIpAddress();
				$lovedOffers = explode(',', $customer->getLovedOffers()); 
                
                $detailsModel = Mage::getModel('popularity/details');
                $popularityDetails = $detailsModel
                        ->getCollection()
                        ->addFieldToFilter('customer_id', $customer->getId())
                        ->addFieldToFilter('popularity_type', $popularityType)
                        ->getFirstItem();
                        
                if (
                		$popularityDetails->getId() || 
                		//in_array($_product->getId(), $popularitySession[$popularityType]) || 
						in_array($_product->getId(), $lovedOffers)
					) {
                    // it was
                    Mage::getSingleton('core/session')->addError('Ai votat deja pentru această ofertă. Voturile multiple nu sunt acceptate.');
                    $response = 'error';
                    
                } else {
                    // it wasn't => save love vote
                    //Mage::log('popularity block customer after voted check - returned "NOT voted" ' . $response .  "\n", false, 'popularity_vote.log');
                    array_push($lovedOffers, $_product->getId());
					$lovedOffers = implode(',', $lovedOffers);
					$customer->setLovedOffers($lovedOffers)->save();
					
                    $OffersPopularity = Mage::getModel('popularity/offer')
                            ->getCollection()
                            ->addFieldToFilter('offer_id', $_product->getId())
                            ->getFirstItem();
                            
                    $loves = $OffersPopularity->getLoves() === null ? 0 : $OffersPopularity->getLoves();
                    $earnedPoints = $OffersPopularity->getEarnedPoints() === null ? 0 : $OffersPopularity->getEarnedPoints();
                    
                    $OffersPopularity->setLoves($loves + 1);
                    $OffersPopularity->setEarnedPoints($earnedPoints + $this->_lovesPoints);
                    $OffersPopularity->setUpdatedAt($now);
                    $OffersPopularity->save();
					
					//Mage::log('popularity block customer after $OffersPopularity saved ' . $response .  "\n", false, 'popularity_vote.log');
                    
                    // add view details
                    $popularityDetails = $detailsModel
                            ->setData('resource', $OffersPopularity->getResourceName())
                            ->setData('popularity_id', $OffersPopularity->getId())
                            ->setData('popularity_type', $popularityType)
                            ->setData('customer_id', $customer->getId())
                            ->setData('customer_ip', $customerIp);
                    $popularityDetails->save();
					
					//Mage::log('popularity block customer after $popularityDetails saved ' . $response .  "\n", false, 'popularity_vote.log');
                    
                    //add the viewed product id to session
                    $popularitySession[$popularityType][] =  $_product->getId();
                    $serializedPopularitySession = serialize($popularitySession);
                    
                    Mage::getSingleton('core/session')->setOffersPopularity($serializedPopularitySession);
                    Mage::getSingleton('customer/session')->unsVoteOffer();
                    
                    $message = 'Votul ofertei <a href="' . Mage::getBaseUrl() . $_product->getUrlPath() . '">' . $_product->getName() . '</a> a fost inregistrat cu success.';
                    Mage::getSingleton('customer/session')->addSuccess($message);
                    
                    $response =  'success~' . $OffersPopularity->getLoves();
                }
            } else {
                // product does not exist
                $response = 'error';
            }
        }
        
        return $response;
    }

    public function addContacts($productId = false)
    {
        //Mage::getSingleton('core/session')->setOffersPopularity(); // resets OffersPopularity session var (for debugging mode)
        if (!$productId) {
            $productId = $this->getRequest()->getParam('product');
        }
        
        
        if ($productId) {
            $_product = Mage::getModel('catalog/product')->load($productId);
            
            if ($_product) {
                $popularitySession = unserialize(Mage::getSingleton('core/session')->getOffersPopularity());
                
                // get/set some current data
                $popularityType = 'contact_views';
                $customerIp = $this->helper('popularity')->getCustomerIpAddress();
                $customerId = $this->_customerId ? $this->_customerId : 0;
                $now = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
                
                if (!isset($popularitySession[$popularityType])) {
                    $popularitySession[$popularityType] = array();
                }
                
				$OffersPopularity = Mage::getModel('popularity/offer')
                        ->getCollection()
                        ->addFieldToFilter('offer_id', $_product->getId())
                        ->getFirstItem();
                        
                /*
                 * check if the current customer has viewed this contact before
                 * based on his ip address and/or customer id
                 */
                if (!in_array($_product->getId(), $popularitySession[$popularityType])) {
                    // it wasn't => update contact views
                    $contactViews = $OffersPopularity->getContactViews() === null ? 0 : $OffersPopularity->getContactViews();
                    $earnedPoints = $OffersPopularity->getEarnedPoints() === null ? 0 : $OffersPopularity->getEarnedPoints();
                    
                    $OffersPopularity->setContactViews($contactViews + 1);
                    $OffersPopularity->setEarnedPoints($earnedPoints + $this->_contactViewsPoints);
                    $OffersPopularity->setUpdatedAt($now);
                    $OffersPopularity->save();
                    
                    $popularityDetails = Mage::getModel('popularity/details')
                            ->setData('resource', $OffersPopularity->getResourceName())
                            ->setData('popularity_id', $OffersPopularity->getId())
                            ->setData('popularity_type', $popularityType)
                            ->setData('customer_id', $customerId)
                            ->setData('customer_ip', $customerIp);
                    $popularityDetails->save();
					
					//add the viewed product id to session
                    $popularitySession[$popularityType][] =  $_product->getId();
                    $serializedOffersPopularity = serialize($popularitySession);
                    Mage::getSingleton('core/session')->setOffersPopularity($serializedOffersPopularity);
                }

				return $OffersPopularity->getEarnedPoints();
            }
        }
    }
}
