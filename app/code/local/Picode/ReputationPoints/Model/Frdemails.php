<?php
class Picode_ReputationPoints_Model_Frdemails extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('reputationpoints/frdemails');
    }

    public function saveEmailData($customerIdentifier, $rppType, $entityId, $postObject)
    {
        $model = $this->setCustomerIdentifier($customerIdentifier)
                      ->setEntityId($entityId)
                      ->setRppType($rppType)
                      ->setSenderFirstname($postObject->getFrdSenderFirstname())
                      ->setSenderLastname($postObject->getFrdSenderLastname())
                      ->setSenderEmail($postObject->getFrdSenderEmail())
                      ->setReceiverEmail($postObject->getFrdReceiverEmail())
                      ->setSubject($postObject->getFrdSubject())
                      ->setMessage($postObject->getFrdMessage());

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $model->setSenderId(Mage::getSingleton('customer/session')->getCustomer()->getId());
        }

        if ($receiverId = Mage::getModel('customer/customer')->loadByEmail($postObject->getFrdReceiverEmail())->getId()) {
            $model->setReceiverId($receiverId);
        }

        try {
            // send email
            $model->save();
            return;

        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            Mage::log('save email faild ' . Zend_Debug::dump($postObject->getData()) . ' with error:', false, 'reputationpoints.log');
            return;
        }

        return;
    }

}