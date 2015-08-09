<?php
class Picode_ReputationPoints_Model_Provider extends Mage_Core_Model_Abstract
{
    protected $_now;
    
    protected function _construct()
    {
        $this->_init('reputationpoints/provider');
        $this->_now = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
    }
    
    public function createNewReputationRecord($_customer)
    {
        $reputation = Mage::getModel('reputationpoints/provider')
            ->setData('provider_id', $_customer->getId())
            ->setData('customer_identifier', $_customer->getCustomerIdentifier())
            ->setData('entity_id', $_customer->getId())
            ->setData('updated_at', $this->_now)
            ->save();
            
        return $reputation;
    }
    
    public function updateRppType($providerId, $update, $entityId)
    {
        Mage::log('updateRppType Provider.php', false, 'debug_20150418158.log');
        $newPoints = Mage::helper('reputationpoints')->getModuleConfig('rpp_settings', $update . '_points');
        $model = Mage::getModel('reputationpoints/provider')->load($entityId, 'entity_id');
        $_customer = Mage::getModel('customer/customer')->load($providerId);

        // set new data
        if (!$model->getId()) {
            /**
             * if (from some reason) provider reputation record does not exist
             * create it now
             */
            $model = Mage::getModel('reputationpoints/provider')->createNewReputationRecord($_customer);
            /**
             * continue updating provider reputation
             */
            $model->load($providerId, 'provider_id');
            $model->setData($update . '_count', 1);
            $model->setData('reputation_points', $newPoints);
            $model->save();
        } else {
            // get current data
            $reputationPoints = $model->getData('reputation_points');
            $typeCount = $model->getData($update . '_count');
            
            // some math
            $newTypeCount = $typeCount + 1;
            $newReputationPoints = $reputationPoints + $newPoints;

//            Zend_Debug::dump($reputationPoints);
//            Zend_Debug::dump($newReputationPoints);
//            Zend_Debug::dump($newPoints);
//            Zend_Debug::dump($typeCount);
//            Zend_Debug::dump($newTypeCount); die('provider update');

            $model->setData($update . '_count', $newTypeCount);
            $model->setData('reputation_points', $newReputationPoints);
            $model->save();
        }
        
        // update reputation table
        $reputation = Mage::getModel('reputationpoints/reputation')->load($providerId, 'provider_id');
        
        if ($reputation) {
            $reputation->setEarnedPoints($reputation->getEarnedPoints() + $newPoints);
            $reputation->save();

            $_customer->setProviderReputation($reputation->getEarnedPoints());
            if ($update == 'view') $_customer->setProviderViews($model->getData($update . '_count'));
            $_customer->save();
            
            $responseArr[$update . '_count'] = $model->getData($update . '_count');
            $responseArr['reputation_count'] = Mage::helper('reputationpoints')->convertReputationPoints($reputation->getData('earned_points'));
        } else {
            return false;
        }
        
        return $responseArr;
    }
    
}