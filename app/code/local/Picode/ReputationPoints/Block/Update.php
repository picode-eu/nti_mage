<?php
class Picode_ReputationPoints_Block_Update extends Mage_Core_Block_Template
{
    protected $_now;
    protected $_timeLimit;

    protected function _construct()
    {
        $this->_now = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()) -3600);
        $this->_timeLimit = Mage::helper('reputationpoints')->getModuleConfig('rpp_settings', 'time_limit');
    }

    public function getLoadEdentityType()
    {
        return $this->getRequest()->getControllerName();
    }

    public function getLoadedEntityId()
    {
        $entityType = $this->getLoadEdentityType();
        $params = $this->getRequest()->getParams();

        switch ($entityType) {
            case 'furnizori':
                return(end($params));
                break;
            case 'product':
                return $params['id'];
                break;
        }
    }

    public function getLoadedEntityDetails($loadedEntityType, $loadedEntityId)
    {
        switch ($loadedEntityType) {
            case 'furnizori':
                $modelType = 'customer/customer';
                break;
            case 'product':
                $modelType = 'catalog/product';
                break;
        }

        return Mage::getModel($modelType)->load($loadedEntityId);
    }

    public function getRppReputation()
    {
        $entityType = $this->_convertReputationType($this->getLoadEdentityType());
        $entityId = $this->getLoadedEntityId();

        switch ($entityType) {
            case 'offer':
                $entityId = Mage::getModel('catalog/product')->load($entityId)->getOfgCustomerId();
                break;
        }

        return Mage::getModel('reputationpoints/reputation')->load($entityId, 'provider_id');
    }

    public function getRppViewCount()
    {
        $entityType = $this->_convertReputationType($this->getLoadEdentityType());
        $entityId = $this->getLoadedEntityId();

        return Mage::getModel('reputationpoints/' . $entityType)
                        ->load($entityId, 'entity_id')
                        ->getViewCount();
    }

    public function getRppSocialsCount()
    {
        $entityType = $this->_convertReputationType($this->getLoadEdentityType());
        $entityId = $this->getLoadedEntityId();

        return Mage::getModel('reputationpoints/' . $entityType)
            ->load($entityId, 'entity_id');
    }

    public function getRppEntityType()
    {
        $entityType = $this->_convertReputationType($this->getLoadEdentityType());
        $entityId = $this->getLoadedEntityId();

        switch ($entityType) {
            case 'offer':
                $entityId = Mage::getModel('catalog/product')->load($entityId)->getOfgCustomerId();
                break;
        }

        return Mage::getModel('reputationpoints/' . $entityType)->load($entityId, 'provider_id');
    }

    public function updateReputation()
    {
        if (!Mage::helper('reputationpoints')->botDetected()) {

            $customerIdentifier = Mage::helper('reputationpoints')->getCustomerIdentifier();
            $params             = $this->getRequest()->getParams();
            $rppType            = $this->_convertReputationType($params['entity_type']);
            $entityId           = $params['entity_id'];
            $update             = $params['update'];

            $reputationUpdated = Mage::getModel('reputationpoints/details')
                ->updateRppDetails($customerIdentifier, $rppType, $entityId, $update);

            if ($reputationUpdated) {
                // return response
                return json_encode($reputationUpdated);
            } else {
                // return response
                $reputationUpdated['no_update'] = 'no update';
                return json_encode($reputationUpdated);
                
                //return false;
            }

            // return response
            $reputationUpdated['no_update'] = 'no update';
            return json_encode($reputationUpdated);
            //return false;
        }

        // return response
        $reputationUpdated['no_update'] = 'no update';
        return json_encode($reputationUpdated);
        //return false;

    }

    private function _convertReputationType($entityType)
    {
        //$entityType = 'product';
        switch ($entityType) {
            case 'furnizori':
                return 'provider';
                break;
            case 'product':
                return 'offer';
                break;
            default:
                return false;
                break;
        }

        return;
    }

    public function getDefault($entity, $attribute)
    {
        if ($entity == 'customer') {
            $session = Mage::getSingleton('customer/session');
            if ($session->isLoggedIn()) {
                $_customer = $session->getCustomer();
                return $_customer->getData('furnizor_contact_' . $attribute) ? $_customer->getData('furnizor_contact_' . $attribute) : $_customer->getData($attribute);
            } else {
                return false;
            }
        }

        return false;
    }
}
