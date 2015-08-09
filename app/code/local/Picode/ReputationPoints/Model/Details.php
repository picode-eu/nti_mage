<?php
class Picode_ReputationPoints_Model_Details extends Mage_Core_Model_Abstract
{
    protected $_now;
    protected $_timeLimit;

    protected function _construct()
    {
        $this->_init('reputationpoints/details');
        $this->_now = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
        $this->_timeLimit = Mage::helper('reputationpoints')->getModuleConfig('rpp_settings', 'time_limit');
    }

    public function updateRppDetails($customerIdentifier, $rppType, $entityId, $update, $error = false)
    {
        $maxLastCreatedDate = strtotime($this->_now) - $this->_timeLimit;
        $error = false;

        if ($update == 'frdemail' && Mage::helper('reputationpoints')->getModuleConfig('rpp_email', 'email_limit')) {
            /*
             * prepare to send link and save message
             */
            $params = Mage::app()->getRequest()->getParams();
            $postObject = new Varien_Object();
            $postObject->setData($params);
            // Zend_Debug::dump($params);
            // Zend_Debug::dump($postObject->getData()); die();

            // validate the form
            if (
                !Zend_Validate::is($postObject->getFrdSenderFirstname(), 'NotEmpty') ||
                !Zend_Validate::is($postObject->getFrdSenderLastname(), 'NotEmpty') ||
                !Zend_Validate::is($postObject->getFrdSubject(), 'NotEmpty')
            )
            {
                $error = true;
                $responseArr['frdemail_sent'] = 'Campurile marcate cu * sunt obligatorii!';
            }

            if (
                !Zend_Validate::is($postObject->getFrdSenderEmail(), 'EmailAddress') ||
                !Zend_Validate::is($postObject->getFrdReceiverEmail(), 'EmailAddress')
                )
            {
                $error = true;
                $responseArr['frdemail_sent'] = 'Verifica adresele de email furnizate!';
            }

            if (!$error) {
                $emailsSent = Mage::getModel('reputationpoints/details')
                    ->getCollection()
                    ->addFieldToFilter('customer_identifier', $customerIdentifier)
                    //->addFieldToFilter('rpp_type', $rppType)
                    //->addFieldToFilter('entity_id', $entityId)
                    ->addFieldToFilter('updated_rpp', $update)
                    ->addFieldToFilter('created_at', array(
                        'from' => date('Y-m-d H:i:s', strtotime($this->_now) - 3600),
                        'to' => $this->_now,
                        'date' => true, // specifies conversion of comparison values
                    ))
                ;

                $existing = $emailsSent->getLastItem();
                $emailsSentCount = (int)$emailsSent->getSize();
                $maxEmailsAllowed = (int)Mage::helper('reputationpoints')->getModuleConfig('rpp_email', 'email_allowed');

                if ($emailsSentCount >= $maxEmailsAllowed) {
                    $maxLastCreatedDate = strtotime($existing->getCreatedAt()) - 1;
                } else {
                    $maxLastCreatedDate = strtotime($existing->getCreatedAt()) + 1;
                }
            } else {
                return $responseArr;
            }

        } elseif ($update == 'ctcemail' && Mage::helper('reputationpoints')->getModuleConfig('rpp_email', 'email_limit')) {
            $emailsSent = Mage::getModel('reputationpoints/details')
                ->getCollection()
                ->addFieldToFilter('customer_identifier', $customerIdentifier)
                //->addFieldToFilter('rpp_type', $rppType)
                //->addFieldToFilter('entity_id', $entityId)
                ->addFieldToFilter('updated_rpp', $update)
                ->addFieldToFilter('created_at', array(
                    'from' => date('Y-m-d H:i:s', strtotime($this->_now) - 3600),
                    'to' => $this->_now,
                    'date' => true, // specifies conversion of comparison values
                ))
            ;
            
            $existing = $emailsSent->getLastItem();
            $emailsSentCount = (int)$emailsSent->getSize();
            $maxEmailsAllowed = (int)Mage::helper('reputationpoints')->getModuleConfig('rpp_email', 'email_allowed');

            if ($emailsSentCount >= $maxEmailsAllowed) {
                $maxLastCreatedDate = strtotime($existing->getCreatedAt()) - 1;
            } else {
                $maxLastCreatedDate = strtotime($existing->getCreatedAt()) + 1;
            }
        } else {
            // get existig details
            $existing = Mage::getModel('reputationpoints/details')
                ->getCollection()
                ->addFieldToFilter('customer_identifier', $customerIdentifier)
                ->addFieldToFilter('rpp_type', $rppType)
                ->addFieldToFilter('entity_id', $entityId)
                ->addFieldToFilter('updated_rpp', $update)
                ->getLastItem()
            ;
        }
        
        //Zend_Debug::dump($existing->getData());

        if ($existing->getRppdetailsId() && strtotime($existing->getCreatedAt()) > $maxLastCreatedDate) {
            $responseArr = false;
            
            if ($update == 'ctcview' && $rppType = 'provider') {
                $responseArr['ctcview_response'] = $this->_getProviderContactHtml($entityId);
            }

            if ($update == 'frdemail') {
                $responseArr['frdemail_sent'] = 'Ai depasit limita de maxim ' . $maxEmailsAllowed . ' mesaje trimise / ora. Te rugam sa incerci mai tarziu.';
            }

            return $responseArr;

        } else {
            $newDetail = Mage::getModel('reputationpoints/details')
                ->setCustomerIdentifier($customerIdentifier)
                ->setRppType($rppType)
                ->setEntityId($entityId)
                ->setUpdatedRpp($update)
                ->save();

            switch ($rppType) {
                case 'provider':
                    $providerId = $entityId;
                    break;
                case 'offer':
                    $providerId = Mage::getModel('catalog/product')->load($entityId)->getOfgCustomerId();
                    break;
            }

            $reputationUpdated = Mage::getModel('reputationpoints/' . $rppType)->updateRppType($providerId, $update, $entityId);

            if (!$error && $update == 'frdemail') {
                // send email to friend
                $emailSent = $this->_sendNewEmail($postObject);
                // store sent data to db
                Mage::getModel('reputationpoints/frdemails')->saveEmailData($customerIdentifier, $rppType, $entityId, $postObject);

                if ($emailSent) {
                    $reputationUpdated['frdemail_sent'] = 'Mesajul a fost trimis cu succes.';
                } else {
                    $reputationUpdated['frdemail_sent'] = 'Mesajul nu a fost trimis din cauza unei erori. Te rugam sa incerci mai tarziu.';
                }
            } elseif ($update == 'ctcview') {
                $reputationUpdated['ctcview_response'] = $this->_getProviderContactHtml($providerId);
            }

            return $reputationUpdated;
        }

        return false;
    }

    private function _getProviderContactHtml($providerId)
    {
        $_provider = Mage::getModel('customer/customer')->load($providerId);

        $output = '';

        if ($_provider->getFurnizorContactFirstname() || $_provider->getFurnizorContactLastname()) {
            $output .= '<li class="contact-name">';
                $output .= '<span class="ion-person"></span>';
                $output .= $_provider->getResource()->getAttribute('furnizor_contact_firstname')->getFrontend()->getValue($_provider);
                $output .= ' ';
                $output .= $_provider->getResource()->getAttribute('furnizor_contact_lastname')->getFrontend()->getValue($_provider);
            $output .= '</li>';
        }

        if ($_provider->getFurnizorContactEmail()) {

            $output .= '<li class="contact-email">';
                $output .= '<span class="ion-at"></span>';
                $output .= $_provider->getFurnizorContactEmail();
            $output .= '</li>';
        }

        if ($_provider->getFurnizorContactPhone()) {

            $output .= '<li class="contact-phone">';
                $output .= '<span class="ion-ios-telephone"></span>';
                $output .= $_provider->getResource()->getAttribute('furnizor_contact_phone')->getFrontend()->getValue($_provider);
            $output .= '</li>';
        }

        if ($_provider->getBusinessNetworksSkype()) {

            $output .= '<li class="contact-phone">';
                $output .= '<span class="ion-social-skype"></span>';
                $output .= $_provider->getResource()->getAttribute('business_networks_skype')->getFrontend()->getValue($_provider);
            $output .= '</li>';
        }

        return $output;
    }

    protected function _sendNewEmail($postObject)
    {
        $senderDetails   = array();
        $receiverDetails = array();
        $emailVariables  = array();

        $senderDetails['sender_fullname']  = ucwords(strtolower($postObject->getData('frd_sender_firstname') . ' ' . $postObject->getData('frd_sender_lastname')));
        $senderDetails['sender_email']     = $postObject->getData('frd_sender_email');
        $receiverDetails['receiver_email'] = $postObject->getData('frd_receiver_email');
        $emailTemplate                     = Mage::getModel('core/email_template')->loadDefault('send_to_friend');
        $emailSubject                      = $senderDetails['sender_fullname'] . ' ți-a trimis un link prin NuntaInImagini.ro';

        // set email template variables
        $emailVariables                    = array_merge($emailVariables, $senderDetails);
        $emailVariables                    = array_merge($emailVariables, $receiverDetails);
        $emailVariables['created_at']      = $this->_now;
        $emailVariables['store_url']       = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $emailVariables['message']         = $postObject->getData('frd_message');
        $emailVariables['email_subject']   = $postObject->getFrdSubject();
        $emailVariables['sender_ip']       = Mage::helper('core/http')->getRemoteAddr(false);
        $emailVariables['entity_details']  = '';

        $baseUrl = Mage::getBaseUrl();
        $_helper = Mage::helper('conturifurnizori');

        // get entiyt details (provider or offer)
        if ($postObject->getEntityType() == 'furnizori') {
            // load entity details
            $entityDetails = Mage::getModel('customer/customer')->load($postObject->getEntityId());
            // set provider variables
            $emailVariables['entity_type'] = 'Furnizorul de Servicii';
            $emailVariables['services'] = $entityDetails->getResource()->getAttribute('furnizor_company_services')->getFrontend()->getValue($entityDetails);
            $emailVariables['entity_details'] .= '<li style="float: left; width: 100%; padding-left: 15px">Denumire: ' . $entityDetails->getData('business_descriptions_title') . ' </li>';
            $emailVariables['entity_details'] .= '<li style="float: left; width: 100%; padding-left: 15px">Sediul: ' .$entityDetails->getFurnizorLocationCity() . '</li>';
            $offersCount = $_helper->countActiveOffers($entityDetails->getId());
            if ($offersCount) $emailVariables['entity_details'] .= '<li style="float: left; width: 100%; padding-left: 15px">Oferte active: ' . $offersCount . '</li>';
            if ($offersCount) $emailVariables['entity_details'] .= '<li style="float: left; width: 100%; padding-left: 15px">Pret mediu / oferta: ' .  Mage::helper('core')->currency(round($_helper->getPriceAvarange($entityDetails->getId())), true, false). ' </li>';
            $emailVariables['entity_details'] .= '<li style="float: left; width: 100%; padding-left: 15px">Experienta: ' . $entityDetails->getResource()->getAttribute('business_descriptions_exp')->getFrontend()->getValue($entityDetails) . ' </li>';
            $emailVariables['entity_url'] = $baseUrl . 'conturifurnizori/furnizori/profil/' . $_helper->seoFriendlyUrl($entityDetails->getData('business_descriptions_title')) . '/' . $entityDetails->getId();
            $emailVariables['entity_name'] = $entityDetails->getData('business_descriptions_title');
        } elseif ($postObject->getEntityType() == 'product') {
            $entityDetails = Mage::getModel('catalog/product')->load($postObject->getEntityId());
            // set provider variables
            $emailVariables['entity_type'] = 'Oferta de Servicii';
            $emailVariables['services'] = $entityDetails->getResource()->getAttribute('ofg_tip_oferta')->getFrontend()->getValue($entityDetails);
            $emailVariables['entity_details'] .= '<li style="float: left; width: 100%; padding-left: 15px">Denumire: ' . $entityDetails->getData('name') . ' </li>';

            $regions = '';
            $regionsArr = explode(',', $entityDetails->getOfgZonaPersonalizata());
            foreach ($regionsArr as $region) {
                $regions .= Mage::getModel('directory/region')->load($region)->getName() .', ';
            }

            $emailVariables['entity_details'] .= '<li style="float: left; width: 100%; padding-left: 15px">Valabila pentru: ' . trim($regions, ', ') . '</li>';
            $emailVariables['entity_details'] .= '<li style="float: left; width: 100%; padding-left: 15px">Pret / Tarif: ' .  Mage::helper('core')->currency($entityDetails->getFinalPrice(), true, false). ' </li>';
            $emailVariables['entity_url'] = $entityDetails->getProductUrl();
            $emailVariables['entity_name'] = $entityDetails->getData('name');
        }

        //load the custom template to email
        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_custom1/name', Mage::app()->getStore()->getId()));
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_custom1/email', Mage::app()->getStore()->getId()));
        $emailTemplate->setReplyTo($senderDetails['sender_email']);
        $emailTemplate->setTemplateSubject($emailSubject);

        try {
            // send email
            $emailTemplate->send($receiverDetails['receiver_email'], $receiverDetails['receiver_email'], $emailVariables);
            return true;

        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            Mage::log('send email faild with error: ' . $errorMessage, false, 'reputationpoints.log');
            return false;
        }

        return false;
    }
}