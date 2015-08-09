<?php
class Picode_CustomerIdentifier_Model_Observer
{
    public function setCustomerIdentifier($observer)
    {
        //$event = $observer->getEvent();
        $customerIp = Mage::helper('core/http')->getRemoteAddr();
        
        if ($this->botDetected()) { // this visit is from a robot
            Mage::log('bot visit ' . $customerIp, false, 'customeridentifier.log');
            return;
        }
        
        $cookie = Mage::getSingleton('core/cookie');
        $session = Mage::getSingleton('customer/session');
        
        Mage::log('start cookie ' . $cookie->get('cstidf'), false, 'customeridentifier.log');
        Mage::log('start session ' . $session->getData('cstidf'), false, 'customeridentifier.log');
        Mage::log('customer ip ' . $customerIp, false, 'customeridentifier.log');
        
        if ($session->isLoggedIn()) {
            // customer is logged in
            Mage::log('customer logged in', false, 'customeridentifier.log');
            $customerIdentifier = $session->getCustomer()->getCustomerIdentifier();
            $cookie->set('cstidf', $customerIdentifier , time() + 31536000, '/');
            $session->setData('cstidf', $customerIdentifier);
            // return;
        } else {
            // customer is not logged in
            Mage::log('customer NOT logged in', false, 'customeridentifier.log');
            if ($cookie->get('cstidf')) {
                // is returning visitor with cookie
                Mage::log('cookie found', false, 'customeridentifier.log');
                $customerIdentifier = $cookie->get('cstidf');
                $session->setData('cstidf', $customerIdentifier);
                // return;
            } elseif ($session->getData('cstidf')) {
                // is returning visitor without cookie
                Mage::log('session found', false, 'customeridentifier.log');
                $customerIdentifier = $session->getData('cstidf');
                $cookie->set('cstidf', $customerIdentifier , time() + 31536000, '/');
                // return;
            } else {
                // check db for ip/identifiere association
                $now = Mage::getModel('core/date')->timestamp(time());
                Mage::log('check db now ' . date('Y-m-d H:i:s', $now), false, 'customeridentifier.log');
                $model = Mage::getModel('customeridentifier/ipidassociation');
                
                $ipIdAssociation = $model->getCollection()
                                         ->addFieldToFilter('customer_ip', $customerIp)
                                         ->setOrder('created_at','DESC')
                                         ->getFirstItem();
                                         
                if ($ipIdAssociation->getId()) {
                    // returnunig visitor with deleted cookie and session
                    $timeDif = $now - strtotime($ipIdAssociation->getCreatedAt());
                    Mage::log('db found', false, 'customeridentifier.log');
                    
                    if ($timeDif < 2551443) { // 30.5 days
                        Mage::log('< 30 days (deleted session)', false, 'customeridentifier.log');
                        $customerIdentifier = $ipIdAssociation->getCustomerIdentifier();
                        $cookie->set('cstidf', $customerIdentifier , time() + 31536000, '/');
                        $session->setData('cstidf', $customerIdentifier);
                        // return;
                    } else {
                        // we could assume that is first visit
                        Mage::log('assumed first visit', false, 'customeridentifier.log');
                        $this->generateCustomerIdentifier($customerIp, $cookie, $session, $model, $now);
                        //return;
                    }
                } else {
                    // this is the first visit
                    Mage::log('first visit', false, 'customeridentifier.log');
                    $this->generateCustomerIdentifier($customerIp, $cookie, $session, $model, $now);
                    //return;
                }
            }
        }
        
        Mage::log('end session ' . $session->getData('cstidf'), false, 'customeridentifier.log');
        Mage::log('end cookie ' . $cookie->get('cstidf'), false, 'customeridentifier.log');
        Mage::log('end' . "\n" . '***', false, 'customeridentifier.log');
        
        return;
    }

    public function updateCustomerIdentifier(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $session = Mage::getSingleton('customer/session');
		$cookie = Mage::getSingleton('core/cookie');
        //Zend_Debug::dump($session->getData());
        
        if (!$customer->getCustomerIdentifier()) {
            // fix for customers which doesn't have a identifier yet
            
            if ($cookie->get('cstidf')) {
                $customerIdentifier = $cookie->get('cstidf');
            } elseif ($session->getData('cstidf')) {
                $customerIdentifier = $session->getData('cstidf');
            }
            
            $customer->setCustomerIdentifier($customerIdentifier)->save();
            
        } else {
            $customerIdentifier = $customer->getCustomerIdentifier();
            $cookie->set('cstidf', $customerIdentifier , time() + 31536000, '/');
            $session->setData('cstidf', $customerIdentifier);
        }
        
        return;
    }
    
    private function generateCustomerIdentifier($customerIp, $cookie, $session, $model, $now)
    {
        $customerIdentifier = $this->generateUnicKey('IDF');
        $cookie->set('cstidf', $customerIdentifier , time() + 31536000, '/');
        $session->setData('cstidf', $customerIdentifier);
        // save data into db
        $model->setData('customer_identifier', $customerIdentifier);
        $model->setData('customer_ip', $customerIp);
        //$model->setData('created_at', date('Y-m-d H:i:s', $now));
        $model->save();
        
        return;
    }
    
    private function generateUnicKey($string = false)
    {
         $unicId = uniqid() . rand(1000, 9999);
        
         if ($string) {
              return $string . $unicId;
         } else {
              return $unicId;
         }
    }
    
    public function botDetected()
    {
        $ip = Mage::helper('core/http')->getRemoteAddr();
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
}
