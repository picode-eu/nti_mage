<?php
class Picode_ReputationPoints_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_visitorIp;
    protected $_blockedIps = array();
    
    public function __construct()
    {
        $this->_visitorIp  = Mage::helper('core/http')->getRemoteAddr(false);
        $this->_blockedIps = explode(',', trim(Mage::getStoreConfig('reputationpoints/rpp_settings/blocked_ips', Mage::app()->getStore())));
    }
    
    /**
     * detect if the visitor is a robot or a real visitor
     */
    public function botDetected()
    {
        if (in_array($this->_visitorIp, $this->_blockedIps) || !isset($_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
        
        if (preg_match('/robot|bot|spider|crawler|curl|^$/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
        
        return true;
    }
    
    public function getModuleConfig($group, $field)
    {
        return Mage::getStoreConfig('reputationpoints' . '/' . $group . '/' . $field, Mage::app()->getStore());
    }
    
    public function getCustomerIdentifier()
    {
        $cookie = Mage::getSingleton('core/cookie');
        $session = Mage::getSingleton('customer/session');
        
        if ($cookie->get('cstidf')) {
            $customerIdentifier = $cookie->get('cstidf');
        } elseif ($session->getData('cstidf')) {
            $customerIdentifier = $session->getData('cstidf');
        } else {
            $model = Mage::getModel('reputationpoints/identifier');
            $customerIdentifier = $model->getCustomerIdentifierByRemoteAddr($this->_visitorIp);
        }
        
        return $customerIdentifier;
    }

    public function convertReputationPoints($points, $decimals = 0)
    {
        $decimals = $points > 999 ? 2 : 0;
        $size = array('','k','M','G','T','P','E','Z','Y');
        $factor = floor((strlen($points) - 1) / 3);
        
        return sprintf("%.{$decimals}f", $points / pow(1000, $factor)) . @$size[$factor];
    }

    public function seoFriendlyUrl($string)
    {
        $string = str_replace(array('[\', \']'), '', $string);
        $string = preg_replace('/\[.*\]/U', '', $string);
        $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
        //$string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
        $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);

        return strtolower(trim($string, '-'));
    }
}
