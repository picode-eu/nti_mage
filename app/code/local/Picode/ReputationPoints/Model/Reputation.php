<?php
class Picode_ReputationPoints_Model_Reputation extends Mage_Core_Model_Abstract
{
    protected $_now;
    
    protected function _construct()
    {
        $this->_init('reputationpoints/reputation');
        $this->_now = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
    }
    
    public function createNewReputationRecord($_customer)
    {
        $reputation = Mage::getModel('reputationpoints/reputation')
            ->setData('provider_id', $_customer->getId())
            ->setData('customer_identifier', $_customer->getCustomerIdentifier())
            ->setData('earned_points', $_customer->getProviderReputation())
            ->setData('updated_at', $this->_now)
            ->save();
            
        return $reputation;
    }

    
}