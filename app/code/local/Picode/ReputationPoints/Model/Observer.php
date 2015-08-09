<?php
class Picode_ReputationPoints_Model_Observer
{
    protected $_now;
    protected $_visitorIp = '';
    protected $_visitorData = array();
    
    // module settings enable/disabled
    protected $_isModuleEnabled   = false;
    protected $_isOfferEnabled    = false;
    protected $_isProviderEnabled = false;
    protected $_timeLimit         = false;
    
    public function __construct()
    {
        $this->_now               = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
        $this->_visitorIp         = Mage::helper('core/http')->getRemoteAddr(false);
        $this->_visitorData       = Mage::getSingleton('core/session')->getVisitorData();
        $this->_isModuleEnabled   = Mage::getStoreConfig('reputationpoints/rpp_settings/enabled_all', Mage::app()->getStore());
        $this->_isOfferEnabled    = Mage::getStoreConfig('reputationpoints/rpp_settings/enabled_offers', Mage::app()->getStore());
        $this->_isProviderEnabled = Mage::getStoreConfig('reputationpoints/rpp_settings/enabled_providers', Mage::app()->getStore());
        $this->_timeLimit         = Mage::getStoreConfig('reputationpoints/rpp_settings/time_limit', Mage::app()->getStore());
    }
    
    /**
     * load customr session
     */
    private function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    /**
     * load cookies
     */
    private function _getCookie()
    {
        return Mage::getSingleton('core/cookie');
    }
    
    /**
     * load identifier model
     */
    private function _getReputationModel($type)
    {
        return Mage::getModel('reputationpoints/' . $type);
    }
    
    /**
     * set customer identifier
     * it will be triggered on every page load
     * to prevent fraud attempts
     */
    public function setCustomerIdentifier(Varien_Event_Observer $observer)
    {
        // check if module is enabled
        if (!$this->_isModuleEnabled) {
            return $this;
        }
        
        $cookie = $this->_getCookie();
        $session = $this->_getSession();
        $model = $this->_getReputationModel('identifier');
        
        // check if the visitor's ip is a valid one and it's not a robot
        if (Mage::helper('core/http')->validateIpAddr($this->_visitorIp) && !Mage::helper('reputationpoints')->botDetected()) {
            
            //try to get customer identifier
            if ($cookie->get('cstidf')) {
                // cookie found
                $customerIdentifier = $cookie->get('cstidf');
                $session->setData('cstidf', $customerIdentifier);
                $model->setExistingCustomerIdentifier($customerIdentifier, $this->_visitorIp);
                //$debug = 'cookie';
            } elseif ($session->getData('cstidf')) {
                // session found
                $customerIdentifier = $session->get('cstidf');
                $cookie->set('cstidf', $customerIdentifier , time() + 31536000, '/');
                $model->setExistingCustomerIdentifier($customerIdentifier, $this->_visitorIp);
                //$debug = 'session';
            } elseif ($model->getCustomerIdentifierByRemoteAddr($this->_visitorIp)) {
                // db record found
                $customerIdentifier = $model->getCustomerIdentifierByRemoteAddr($this->_visitorIp);
                $cookie->set('cstidf', $customerIdentifier , time() + 31536000, '/');
                $session->setData('cstidf', $customerIdentifier);
                //$debug = 'model';
            } else {
                // new visitor
                $customerIdentifier = $this->_generateUniqueId();
                $cookie->set('cstidf', $customerIdentifier , time() + 31536000, '/');
                $session->setData('cstidf', $customerIdentifier);
                $model->setNewCustomerIdentifier($customerIdentifier, $this->_visitorIp);
                //$debug = 'new';
            }
            
            //echo $debug . ' ' . $customerIdentifier;
        } else {
            Mage::log('Robot visit (Picode_ReputationPoints_Model_Observer setCustomerIdentifier) ' . $this->_visitorIp, false, 'debug_20150418158.log');
        }
        
        return $this;
    }
    
    /**
     * update customer data after customer login action
     */
    public function updateCustomerIdentifier(Varien_Event_Observer $observer)
    {
        $_customer = $observer->getEvent()->getCustomer();
        $customerIdentifier = Mage::helper('reputationpoints')->getCustomerIdentifier();

        $identifier = $this->_getReputationModel('identifier');
        $identifier->updateCustomerData($_customer, $customerIdentifier, $this->_visitorIp);

        return $this;
    }

    /**
     * generate an unique key
     */
    private function _generateUniqueId()
    {
        return md5(time() . uniqid());
    }

    /**
     * set customer identifier from cookie, session or db
     * after customer create success action
     */
    public function createProviderReputation(Varien_Event_Observer $observer)
    {
        // check if module is enabled
        if (!$this->_isModuleEnabled) {
            return $this;
        }

        $_customer = $observer->getEvent()->getProvider();
        $customerIdentifier = Mage::helper('reputationpoints')->getCustomerIdentifier();

        Mage::log('New provider (Picode_ReputationPoints_Model_Observer createProviderReputation start) ' . $_customer->getId(), false, 'reputationpoints.log');

        // update customer data
        $_customer->setCustomerIdentifier($customerIdentifier);
        $_customer->save();

        // creat reputation record
        $model = $this->_getReputationModel('reputation');
        $model->createNewReputationRecord($_customer);

        // create provider reputation record
        $model = $this->_getReputationModel('provider');
        $model->createNewReputationRecord($_customer);

        // create offer reputation record !!! moved into Observer.php !!!
        // $model = $this->_getReputationModel('offer');
        // $model->createNewReputationRecord($_customer);

        // Mage::log('New reputation (Picode_ReputationPoints_Model_Observer createProviderReputation end) ' . $model->getId(), false, 'reputationpoints.log');
        
        return $this;
    }

    public function createOfferReputation(Varien_Event_Observer $observer)
    {
        // check if module is enabled
        if (!$this->_isModuleEnabled) {
            return $this;
        }

        $_offer = $observer->getEvent()->getOffer();

        Mage::log('New offer (Picode_ReputationPoints_Model_Observer createOfferReputation start) ' . $_offer->getId(), false, 'debug_20150418158.log');

        // create provider reputation record
        $model = $this->_getReputationModel('offer');
        $model->createNewReputationRecord($_offer);

        return $this;
    }
    
    /**
     * update offers view
     */
    public function updateOfferViewCount(Varien_Event_Observer $observer)
    {
        // check if module is enabled
        if (!$this->_isModuleEnabled || !$this->_isOfferEnabled) {
            return $this;
        }
        
        $_product = $observer->getEvent()->getProduct();
        
        if ($_product->getTypeId() == 'ofertefurnizori') {
           
        }
    }
    
    /**
     * update ctcemail after success mesage
     */
     
    public function updateCtcemailCount(Varien_Event_Observer $observer)
    {
        // check if module is enabled
        if (!$this->_isModuleEnabled) {
            return $this;
        }
        
        $dataSent = $observer->getEvent()->getData();
        $customerIdentifier = Mage::helper('reputationpoints')->getCustomerIdentifier();
        $rppType = $dataSent['product'] ? 'offer' : 'provider';
        $providerId = $dataSent['provider_id'];
        $entityId = $dataSent['provider_id'];
        $update = 'ctcemail';
        $error = $dataSent['error'];
        
        $model = $this->_getReputationModel('details');
        $model->updateRppDetails($customerIdentifier, $rppType, $entityId, $update);
        
        return $this;
    }
}
