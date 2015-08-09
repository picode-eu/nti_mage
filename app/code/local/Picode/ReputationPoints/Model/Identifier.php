<?php
class Picode_ReputationPoints_Model_Identifier extends Mage_Core_Model_Abstract
{
    protected $_now;
    
    protected function _construct()
    {
        $this->_init('reputationpoints/identifier');
        $this->_now = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
    }
    
    public function setNewCustomerIdentifier($customerIdentifier, $customerIp)
    {
        $identifier = Mage::getModel('reputationpoints/identifier')
                ->setData('customer_identifier', $customerIdentifier)
                ->setData('customer_ip', $customerIp)
                ->setData('updated_at', $this->_now)
                ;
                
        $identifier->save();
        
        return;
    }
    
    public function getCustomerIdentifierByRemoteAddr($visitorIp)
    {
        $lastUpdate = date('Y-m-d H:i:s', strtotime($this->_now) - 3600);
        
        $identifier = Mage::getModel('reputationpoints/identifier')
                ->getCollection()
                ->addFieldToFilter('customer_ip', $visitorIp)
                ->addFieldToFilter('updated_at', array('gt' => $lastUpdate))
                ->getLastItem();
                
        if ($identifier->getId()) {
            $identifier->setData('updated_at', $this->_now);
            $identifier->save();
            
            return $identifier->getCustomerIdentifier();
        }
        
        return false;
    }
    
    public function setExistingCustomerIdentifier($customerIdentifier, $customerIp)
    {
        $identifier = Mage::getModel('reputationpoints/identifier')
                ->getCollection()
                ->addFieldToFilter('customer_identifier', $customerIdentifier)
                ->getLastItem();
        
        if ($identifier->getId()) {
            /*
             * update customer ip and updated date and time
             */
            $identifier->setData('customer_ip', $customerIp)
                ->setData('updated_at', $this->_now)
                ->save();
            
            return;
        } else {
            /*
             * reputation table was truncated
             * or the record was lost from some reason
             */
            $identifier = Mage::getModel('reputationpoints/identifier')
                    ->setData('customer_identifier', $customerIdentifier)
                    ->setData('customer_ip', $customerIp)
                    ->setData('updated_at', $this->_now)
                    ;
                    
            $identifier->save();
            
            return;
        }
        
        return;
    }

    public function updateCustomerIdentifier($_customer, $currentIdentifier, $oldIdentifier, $customerIp)
    {
        $isProvider = $_customer->getGroupId() == 4 ? 1 : 0;
        
        $identifier = Mage::getModel('reputationpoints/identifier')
                ->getCollection()
                ->addFieldToFilter('customer_identifier', $currentIdentifier)
                ->getLastItem();
                
        if ($identifier->getId()) {

            $identifier
                ->setData('customer_identifier', $oldIdentifier)
                ->setData('is_provider', $isProvider)
                ->setData('customer_ip', $customerIp)
                ->setData('updated_at', $this->_now)
                ->save();
        }
        
        return;
    }

    public function updateCustomerData($_customer, $customerIdentifier, $customerIp)
    {
        $isProvider = $_customer->getGroupId() == 4 ? 1 : 0;
        
        $identifier = Mage::getModel('reputationpoints/identifier')
                ->getCollection()
                ->addFieldToFilter('customer_identifier', $customerIdentifier)
                ->getLastItem();

        /**
         * customer identifier comes from cookie then from session then from db
         * we assume that the customer identifier is unique
         * so, we should know if there are customers who have two or more accounts
         */
        if (!$identifier->getCustomerId()) { // this is the first log in or is an existing visitor from another machine
            // check for existing visitor
            Mage::log('check for existing = ' . $_customer->getId(), false, 'debug_20150418.log');
            $existing = Mage::getModel('reputationpoints/identifier')
                ->getCollection()
                ->addFieldToFilter('customer_id', $_customer->getId())
                ->getLastItem();

            if ($existing) { // it is a returning visitor from another machine
                // correct the visitor identifier
                Mage::log('it is an existing = ' . $existing->getId(), false, 'debug_20150418.log');
                Mage::getSingleton('customer/session')->setData('cstidf', $_customer->getCustomerIdentifier());
                Mage::getSingleton('core/cookie')->set('cstidf', $_customer->getCustomerIdentifier() , time() + 31536000, '/');
                $lastLogin = $existing->getLastLogin();
                $existing->setLastLogin($this->_now)->save();
            } else { // this is the first log in
                Mage::log('this is first log in = ' . $customerIdentifier, false, 'debug_20150418.log');

                $identifier
                    ->setData('is_provider', $isProvider)
                    ->setData('customer_id', $_customer->getId())
                    ->setData('customer_ip', $customerIp)
                    ->setData('last_login', $this->_now)
                    ->setData('updated_at', $this->_now);
                $identifier->save();

                $lastLogin = $identifier->getLastLogin();
            }

        } else { // it is a returning visitor from a registered machine
            $lastLogin = $identifier->getLastLogin();
            $identifier->setLastLogin($this->_now)->save();
        }

        $_helper = Mage::helper('reputationpoints');
        $current = strtotime(date('Y-m-d H:i:s', strtotime($this->_now)));
        $lastLogin = strtotime(date('Y-m-d H:i:s', strtotime($lastLogin)));
        $diff = $current - $lastLogin;
        $newPoints = 0;

        Mage::log('customer = ' . $_customer->getId(), false, 'debug_20150418.log');
        Mage::log('current = ' . $current, false, 'debug_20150418.log');
        Mage::log('last = ' . $lastLogin, false, 'debug_20150418.log');
        Mage::log('diff = ' . $diff, false, 'debug_20150418.log');

        if ($diff) {
            switch ($diff) {
                case ($diff > '86399' && $diff < '172800'): // zilnic
                    Mage::log('case 1', false, 'debug_20150418.log');
                    $newPoints = $_helper->getModuleConfig('rpp_login', 'login_daily');
                    break;
                case ($diff > '172799' && $diff < '259200'): // la doua zile
                case ($diff > '259199' && $diff < '345600'): // la trei zile
                    Mage::log('case 2-3', false, 'debug_20150418.log');
                    $newPoints = $_helper->getModuleConfig('rpp_login', 'login_2_3');
                    break;
                case ($diff > '345599' && $diff < '432000'): // la 4 zile
                case ($diff > '431999' && $diff < '518400'): // la 5 zile
                    Mage::log('case 4-5', false, 'debug_20150418.log');
                    $newPoints = $_helper->getModuleConfig('rpp_login', 'login_4_5');
                    break;
                case ($diff > '518399'): // mai mult de 5 zile
                    Mage::log('case 6+', false, 'debug_20150418.log');
                    $newPoints = $_helper->getModuleConfig('rpp_login', 'login_6_plus');
                    break;
                default:
                    Mage::log('case default', false, 'debug_20150418.log');
                    $newPoints = 0;
                    break;
            }

            if ($newPoints) {
                $reputationPoints = Mage::getModel('reputationpoints/reputation')->load($_customer->getId(), 'provider_id');
                $reputationPoints
                    ->setEarnedPoints($reputationPoints->getEarnedPoints() + $newPoints)
                    ->setUpdatedAt($this->_now)
                    ->save();

                $reputationPoints = Mage::getModel('reputationpoints/reputation')->load($_customer->getId(), 'provider_id');

                $_customer->setProviderReputation($reputationPoints->getEarnedPoints())->save();
            } else {
                Mage::log('no newPoints', false, 'debug_20150418.log');
                //$identifier->setLastLogin($identifier->getLastLogin());
            }
        } else {
            Mage::log('no diff', false, 'debug_20150418.log');
        }

        Mage::log('new points = ' . $newPoints, false, 'debug_20150418.log');
        
        return;
    }    
    
}
