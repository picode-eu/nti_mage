<?php
class Picode_ConturiFurnizori_InregistrareController extends Mage_Checkout_Controller_Action
{
    // /**
     // * Check if customer is logged in
     // * and redirect to his account page if is
     // */
    // public function preDispatch()
    // {
        // parent::preDispatch();
//      
        // if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            // /**
             // * redirect customer if he/she is already logged in
             // * he should not be able to reach the register form
             // * he should be redirected to upgrade form instead
             // */
            // if ($this->getRequest()->getActionName() == 'index') {
                // $this->_redirect('customer/account/');
            // }
        // }
    // }
    
    // /**
     // * Check if customer is logged in or not
     // * If not logged in then redirect to customer login
     // */
    // public function preDispatch()
    // {
        // parent::preDispatch();
//      
        // if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            // $this->setFlag('', 'no-dispatch', true);
        // }
    // }
    
    /**
     * Get one page checkout model
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
    
    public function indexAction()
    {
        $this->loadLayout();
        
        if (!$this->getOnepage()->getQuote()->getId()) {
            $this->_redirect('conturifurnizori/produse/list/');
            return;
        } 
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->_initLayoutMessages('catalog/session');
    
        $this->getLayout()->getBlock('head')->setTitle($this->__('Inregistrare furnizori'));
        $this->renderLayout();
    }
    
    private function _getBlock()
    {
        return $this->getLayout()->createBlock('conturifurnizori/conturi_inregistrare');
    }
    
    /**
     * save personal info
     */
    public function processAjaxDataAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Save new customer and his order
     */
    public function saveAccountAction()
    {
        // set primary data
        $now = date("Y-m-d 00:00:00",  strtotime(Mage::getModel('core/date')->date('Y-m-d H:i:s') . ' -1 hour'));
        $quote = $this->getOnepage()->getQuote();
        $data = $this->getRequest()->getParams();
        $newCustomer = false;
        
        if (!isset($data['accept_terms'])) {
            Mage::getSingleton('core/session')->addError('Pentru a te inregistra, trebuie sa fi de acord cu <a href="' . Mage::getBaseUrl() . 'termeni-si-conditii-de-utilizare/" target="_blank">Termenii si Conditiile</a> de utilizare');
            $this->_redirect('conturifurnizori/inregistrare/');
            
            return;
        }
        
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->_redirect('conturifurnizori/produse/list/');
            // debug log
            Mage::log('$quote has errors ' . $quote->getId(), false, 'providerRegistrationDebug.log');
            
            return;
        }
        
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $quote->setCheckoutMethod('register');
        } else {
            $quote->setCheckoutMethod('update');
        }
        
        $checkoutMethod = $quote->getCheckoutMethod();
        
        // check if customer exists or not
        $customer = Mage::getModel('customer/customer');
        
        if ($customer->loadByEmail($quote->getCustomerEmail())->getId() && $quote->getCheckoutMethod() != 'update') {
            /**
             * customer already exists
             * redirect to provider registration with error message
             */
            $errorMessage  = 'Există deja un cont creat cu adresa de email: ' . $quote->getCustomerEmail() . '.';
            $errorMessage .= '<br /><a href="' . $this->getLoginUrl() . '">Autentifică-te</a> sau solicită <a href="' . $this->getResetPasswordUrl() . '">resetarea parolei</a> dacă nu ţi-o mai aminteşti.';
            
            Mage::getSingleton('core/session')->addError($errorMessage);
            $this->_redirect('*/*/');
            
            return ;
        } else {
            
            if ($quote->getCheckoutMethod() != 'update') {
                /**
                 * customer does not exist
                 * create new customer
                 */

                $newCustomer = true;
                $earlyBird = Mage::getSingleton('customer/session')->getIsEarlyBird() ? true : false;
                // $customer->setGroupId(4); // 4 is for Furnizori
                $customer->setGroupId(1); // 1 is for General
                $customer->setEmail($quote->getCustomerEmail());
                $customer->setFirstname(ucfirst($quote->getCustomerFirstname()));
                $customer->setLastname(ucfirst($quote->getCustomerLastname()));
                $customer->setPasswordHash($quote->getPasswordHash());
                // set additional customer attributes
                $customer->setBusinessDescriptionsTitle($quote->getBusinessDescriptionsTitle());
                
                if ($quote->getBillingAddress()->getCompany() != NULL)
                    $customer->setFurnizorCompanyName($quote->getBillingAddress()->getCompany());
                else
                    $customer->setFurnizorCompanyName($quote->getBusinessDescriptionsTitle());
                
                $customer->setFurnizorCompanyType($quote->getFurnizorCompanyType());
                $customer->setFurnizorCompanyServices($quote->getFurnizorCompanyServices());
                $customer->setBusinessDescriptionsExp($quote->getBusinessDescriptionsExp());

                if ($earlyBird) {
                    if ($quote->getBusinessDescriptionsDesc())
                        $customer->setBusinessDescriptionsDesc($quote->getBusinessDescriptionsDesc());
                }
                
                if ($quote->getFurnizorContactFirstname())
                    $customer->setFurnizorContactFirstname($quote->getFurnizorContactFirstname());
                
                if ($quote->getFurnizorContactLastname())
                    $customer->setFurnizorContactLastname($quote->getFurnizorContactLastname());
                
                $customer->setFurnizorContactEmail($quote->getFurnizorContactEmail());
                if ($quote->getFurnizorContactPhone())
                    $customer->setFurnizorContactPhone($quote->getFurnizorContactPhone());
                
                $customer->setFurnizorLocationCity($quote->getFurnizorLocationCity());
                $customer->setFurnizorLocationProvince($quote->getFurnizorLocationProvince());
                
                if ($quote->getFurnizorLocationAddress())
                    $customer->setFurnizorLocationAddress($quote->getFurnizorLocationAddress());
                
                if ($quote->getFurnizorLocationNumber())
                    $customer->setFurnizorLocationNumber($quote->getFurnizorLocationNumber());

                // get and set customer identifier !!! it was moved to RuputationPoints Module
                // $cookie = Mage::getSingleton('core/cookie');
                // $session = Mage::getSingleton('customer/session');

                // if (Mage::getSingleton('core/cookie')->get('cstidf')) {
                    // $customer->setCustomerIdentifier($cookie->get('cstidf'));
                // } elseif ( Mage::getSingleton('customer/session')->getData('cstidf')) {
                    // $customer->setCustomerIdentifier($session->getData('cstidf'));
                // }
            }

            // prepare to set customer account options
            $items = $quote->getAllItems();
            $catalogHelper = Mage::helper('catalog/product_configuration');
            // get item options value
            foreach($items as $item) {
                // prepare to set customer account options
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                // set customer account options
                $this->setCustomerAccountOptions($customer, $product);
                // check if product has custom options
                if ($product->getHasOptions()) {
                    $options = $catalogHelper->getCustomOptions($item);
                    $optionValue = (int)$options[0]['value']; // used for saving account expiration
                }
            }
            
            /**
             * check if the checkout process is an account update
             */
            if ($quote->getCheckoutMethod() == 'update') {
                // get old expiration date
                $expiration = $customer->getFurnizorAccountExpiration();
                if (strtotime($now) < strtotime($expiration)) {
                    // expiratin date is in the future => the update should start form expiration date
                    $now = $expiration;
                }
            }
            
            /**
             * check if the new created account is a paid one
             */
            if ($paidAccount = Mage::helper('conturifurnizori')->accountIsPaid()) {
                // it is a paid one
                $customer->setFurnizorAccountType(2); // 2 is for Paid Account
                $customer->setFurnizorAccountStatus(2); // 2 is for Pending Payment
                $customer->setFurnizorAccountExpiration(date('Y-m-d 00:00:00', strtotime('+' . $optionValue . ' month', strtotime($now))));
            } else {
                // it is a free one
                $customer->setFurnizorAccountType(1); // 1 is for Free Account
                $customer->setFurnizorAccountStatus(3); // 3 is for Pending Approval
                $customer->setFurnizorAccountExpiration(date('Y-m-d 00:00:00', strtotime('+1 years', strtotime($now))));
            }
            
            if (!$quote->getChechoutMethod() == 'update') {
                $customer->setFurnizorAccountOnlineStatus(0); // 0 is for private
            }
            
            try {
                // save the newly created customer
                $customer->setCreatedAt(date("Y-m-d H:i:s",  strtotime(Mage::getModel('core/date')->date('Y-m-d H:i:s') . ' +2 hour')));
                $customer->setConfirmation(null);
                $customer->sendNewAccountEmail();
                // save the newly created customer
                $customer->save();
                // set customer id to quote
                $quote->setCustomerId($customer->getId());
                $quote->save();

                if ($earlyBird) {
                    // save and move uploaded logo image
                    if ($quote->getBusinessImagesLogo()) {
                        // move logo image to the final location
                        $tempUploadDir = Mage::getBaseDir('media') . DS . 'tmp_uploads' . DS . 'tmp_logos' . DS;
                        $newFileName = $this->removeSpecialCharacters($customer->getBusinessDescriptionsTitle()). '-logo-' . time();

                        if ($logo = $this->moveUploadedFile($tempUploadDir, $quote->getBusinessImagesLogo(), $newFileName)) {
                            $customer->setData('business_images_logo', $logo);
                        }
                    }
                }
                
                /**
                 * create provider reputation entry
                 */
                // it was moved to ReputationPoints modul
                // $reputation = Mage::getModel('providerreputation/reputation')
                    // ->setProviderId($customer->getId());
                
                // add reputation bonus points for paid accounts
                // if ($quote->getCheckoutMethod != 'update')
                    // $reputation->setProviderReputation($quote->getSubtotal() * 10);

                // $reputation->save();
                
                // $customer
                    // ->setProviderReputation($quote->getSubtotal() * 10)
                    // ->save();
                
                if ($newCustomer) {
                    /**
					 * Make a "login" of the newly created customer
					 * I had to move it after the order was placed
					 */
                    // Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
                    // debug log
                    Mage::log('customer created ' . $customer->getId(), false, 'providerRegistrationDebug.log');
                }
                
            } catch (Exception $ex) {
                
                // debug log
                Mage::log('customer creation faild ' . $ex->getMessage(), false, 'providerRegistrationDebug.log');
                // redirect to provider registration with error message
                $this->_redirect('conturifurnizori/inregistrare/');
                
                return;
            }
            
            // save default billing address
            if ($paidAccount && $quote->getGrandTotal() > 0) {
                // account type is a paid one and does not have a trial period activated
                Mage::log('account type is a paid one and does not have a trial period activated ' . $quote->getGrandTotal(), false, 'providerRegistrationDebug.log');
                $this->saveBillingAddress($customer, $data, $quote);
                
            } elseif ($paidAccount && $quote->getGrandTotal() == 0) {
                // account type is a paid one with 100% discount coupon
                Mage::log('account type is a paid one with 100% discount coupon', false, 'providerRegistrationDebug.log');
                $payment = $quote->getPayment();
                $payment->setMethod('free');
                $payment->save();
                
                $this->setNonRequiredBilling($quote);
            } else {
                // account type is a free one
                Mage::log('account type is a free one', false, 'providerRegistrationDebug.log');
                $payment = $quote->getPayment();
                $payment->setMethod('free');
                $payment->save();
                
                $this->setNonRequiredBilling($quote);
            }
            
            $customer->save();

        } // ends create new customer
        
        // convert quote to order
        $quote->setTotalsCollectedFlag(false)->collectTotals();
        $quote->save();
        
        $service = Mage::getModel('sales/service_quote', $quote);
        
        try {
            $service->submitAll();
            $quote->setIsActive(false);
            $quote->save(); // comment out this line during testing
            
            $order = $service->getOrder();
            $incrementId = $order->getIncrementId();
            
            Mage::getSingleton('customer/session')->setLastIncrementId($incrementId);
            Mage::log('grand total = ' . $order->getGrandTotal(), false, 'providerRegistrationDebug.log');
            
            /**
             * set order as complete if account type is a free one
             * or grand total is zero (trial or 100% discount)
             */
            if (!$paidAccount || $quote->getGrandTotal() == 0) {
                $order->setData('state', 'complete');
                $order->setStatus('complete');
                $order->save();
            }
            
            /**
             * set customer as active if account type is paid
             * and grand total is zero (trial or 100% discount)
             */
            if ($paidAccount && $quote->getGrandTotal() == 0) {
                $customer->setFurnizorAccountStatus(1); // 1 is for active
                $customer->save();
            }
            
            /**
             * get payment methode and rdirect customer
             * to payment gataway if needed
             */
            if ($paidAccount && $quote->getGrandTotal() > 0) {
                $paymentMethod = $quote->getPayment()->getMethod();
                /**
                 * there is only two payment methods enabled
                 * 1. EuPlatesc.ro with code "ep_initialize"
                 * 2. Bank Transfer Payment with code "banktransfer"
                 */
                if ($paymentMethod == 'ep_initialize') { // redirect to euplatesc.ro
                    $this->_redirect('ep/initialize/payment/');
                
                    return;
                    
                } else {
                    $customer->setFurnizorAccountTrialExp(date('Y-m-d 00:00:00', strtotime('+5 days', strtotime($now))));
                    $customer->save();
                    
                    #TODO: trimite factura proforma  
                }
            }
            
            // set reputation bonus based on account type created
            foreach($order->getAllVisibleItems() as $_item) {
                $_product = Mage::getModel('catalog/product')->load($_item->getProductId());
                
                if ($_product->getContAcordareRpf() == '58') { // percent
                    $rpfPoints = $_item->getOriginalPrice() * $_product->getContPuncteRpf() / 10; 
                } elseif ($_product->getContAcordareRpf() == '59') { // fix
                    $rpfPoints = $_product->getContPuncteRpf();
                }
            }
            
            $customer->setProviderReputation($rpfPoints);
            $customer->setProviderCredits(0);
            $customer->setProviderViews(0);
            
            // correct customer group
            $customer->setGroupId(4); // 4 is for Furnizori
            $customer->save();

            // fire an event
            Mage::dispatchEvent('provider_registration_after', array('provider' => $customer));
            /**
             * Make a "login" of the newly created customer
             * only if it is not an early bird!
             */
            if (!Mage::getSingleton('customer/session')->getIsEarlyBird()) {
                Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
            }

            // debug log
            Mage::log('new order was placed ' . $incrementId, false, 'providerRegistrationDebug.log');
            Mage::app()->getFrontController()->getResponse()->setRedirect($data['success_url']); 
            
            return;
            
        } catch (Exception $ex) {
            
            // debug log
            Mage::log('new order error ' . $ex->getMessage(), false, 'providerRegistrationDebug.log');
            Mage::app()->getFrontController()->getResponse()->setRedirect($data['failure_url']); 
            
            return;
        }
        
        return;
    }

    public function saveBillingAddress($customer, $data, $quote)
    {
        $quoteAddress = $quote->getBillingAddress();
        
        if ($quoteAddress) {
            // set names
            $saveQuoteAddress = false;
            
            if ($quoteAddress->getFirstname() === NULL) {
                $quoteAddress->setFirstname($quote->getCustomerFirstname());
                $saveQuoteAddress = true;
            }
            
            if ($quoteAddress->getLastname() === NULL) {
                $quoteAddress->setLastname($quote->getCustomerLastname());
                $saveQuoteAddress = true;
            }
            
            if ($quoteAddress->getPostcode() === NULL) {
                $postcode = $customer->getPostcode() ? $customer->getPostcode() : 'nespecificat';
                $quoteAddress->setPostcode($postcode);
                $saveQuoteAddress = true;
            }
            
            if ($saveQuoteAddress) {
                $quoteAddress->save();
            }
            
            $billingAddress = array (
                'firstname'     => ucfirst($quoteAddress->getFirstname()),
                'lastname'      => ucfirst($quoteAddress->getLastname()),
                'company'       => $quoteAddress->getCompany(),
                'street'        => $quoteAddress->getStreet(),
                'city'          => $quoteAddress->getCity(),
                'country_id'    => $quoteAddress->getCountryId(),
                'region_id'     => $quoteAddress->getRegionId(),
                'region'        => $quoteAddress->getRegion(),
                'postcode'      => $quoteAddress->getPostcode(),
                'telephone'     => $quoteAddress->getTelephone(),
                'billing_tip'   => $quoteAddress->getBillingTip(),
                'billing_cui'   => $quoteAddress->getBillingCui(),
                'billing_nrc'   => $quoteAddress->getBillingNrc(),
                'billing_banca' => $quoteAddress->getBillingBanca(),
                'billing_iban'  => $quoteAddress->getBillingIban(),
                'billing_ci'    => $quoteAddress->getBillingCi(),
                'billing_cnp'   => $quoteAddress->getBillingCnp(),
                'street_other'  => $quoteAddress->getStreetOther()                
            );
            
            /**
             * check if the checkout is an update
             * and if the customer has billig address
             */
            if ($quote->getData('checkout_method') == 'update') {
                // the checkout is an update
                $customerAddressId = $customer->getDefaultBilling();
                if ($customerAddressId){
                    // the customer has a billing address => update it
                    $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
                } else {
                    // the customer does not have a billing address => save a new one
                    $customerAddress = Mage::getModel('customer/address');
                    $customerAddress->setCustomerId($customer->getId());
                }
            } else {
                // the checkout is a new account creation
                $customerAddress = Mage::getModel('customer/address');
                $customerAddress->setCustomerId($customer->getId());
            }
            
            foreach ($billingAddress as $name => $value) {
                $customerAddress->setData($name, $value); 
            }
            
            $customerAddress
                ->setIsDefaultBilling(true)
                ->setSaveInAddressBook(true);
                    
            try {
                $customerAddress->save();
                // debug log
                Mage::log('customer address saved ' . $customerAddress->getId(), false, 'providerRegistrationDebug.log');
            }
            catch (Exception $ex) {
                // debug log
                Mage::log('customer address error ' . $ex->getMessage(), false, 'providerRegistrationDebug.log');
                // redirect to provider registration with error message
                Mage::getSingleton('core/session')->addError($ex->getMessage());
                $this->_redirect('conturifurnizori/inregistrare/');
                
                return ;
            }
        }

        return;
    }

    public function setNonRequiredBilling($quote) 
    {
        // set non-required billing address
        $addressesCollection = $quote->getAddressesCollection();
        
        foreach ($addressesCollection as $address) {
            if ($address['address_type'] == 'billing') {
                $billingAddress = array (
                    'firstname' => ucfirst($quote->getCustomerFirstname()),
                    'lastname' => ucfirst($quote->getCustomerLastname()),
                    'street' => 'not required',
                    'city' => 'not required',
                    'country_id' => 'RO',
                    'region_id' => 'not required',
                    'region' => 'not required',
                    'postcode' => 'not required',
                    'telephone' => 'not required',
                );
                
                foreach ($billingAddress as $name => $value) {
                    $address->setData($name, trim($value));
                }
                
                $address->save(); // comment out this line during testing
            }
        }
        
        return;
    }

    public function setCustomerAccountOptions($customer, $product)
    {
        $customer->setFurnizorAccountLevel($product->getContLevel());
        // is enabled testing mode?
        if ($product->getContActiveazaTestare()) {
            $customer->setFurnizorAccountTrialLevel($product->getContLevel());
            // get current timestamp
            $perioadaTestare = (int)$product->getContPerioadaTestare();
            $now = date('Y-m-d 00:00:00', Mage::getModel('core/date')->timestamp(time()));
            $customer->setFurnizorAccountTrialExp(date('Y-m-d 00:00:00', strtotime('+' . $perioadaTestare . ' month', strtotime($now))));
        }
        $customer->setAcOpAfisareProfil($product->getContAfisareProfil());
        $customer->setAcOpAfisarePreferentiala($product->getContAfisarePreferentiala());
        $customer->setAcOpAfisareOferte($product->getContAfisareOferte());
        $customer->setAcOpMaxOferteActive($product->getContMaxOferteActive());
        $customer->setAcOpEtichetaOfertaSpeciala($product->getContEtichetaOfertaSpeciala());
        $customer->setAcOpLinkRestulOfertelor($product->getContLinkRestulOfertelor());
        $customer->setAcOpLinkAlteOferte($product->getContLinkAlteOferte());
        $customer->setAcOpAfisareAlbumPrezentare($product->getContAfisareAlbumPrezentare());
        $customer->setAcOpMaxAlbumActive($product->getContMaxAlbumActive());
        $customer->setAcOpSpatiuDisc($product->getContSpatiuDisc());
        $customer->setAcOpAfisareVideoPrezentare($product->getContAfisareVideoPrezentare());
        $customer->setAcOpMaxVideoActive($product->getContMaxVideoActive());
        $customer->setAcOpNotificariSms($product->getContNotificariSms());
        $customer->setAcOpNotificariEmail($product->getContNotificariEmail());
        $customer->setAcOpLinkDirectWebsite($product->getContLinkDirectWebsite());
        $customer->setAcOpAfisareRetele($product->getContAfisareRetele());
        $customer->setAcOpRapoarteAvansate($product->getContRapoarteAvansate());
        
        return $customer;
    }

    public function succesAction()
    {
        $this->loadLayout();
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
    
        $this->getLayout()->getBlock('head')->setTitle($this->__('Inregistrare cu succes'));
        $this->renderLayout();
    }
    
    public function eroareAction()
    {
        $this->loadLayout();
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
    
        $this->getLayout()->getBlock('head')->setTitle($this->__('Eroare la inregistrare'));
        $this->renderLayout();
    }

    public function getLoginUrl()
    {
        return Mage::getUrl('customer/account/login/', array('_secure' => true));
    }
    
    public function getResetPasswordUrl()
    {
        return Mage::getUrl('customer/account/forgotpassword/', array('_secure' => true));
    }

    public function logouploadAction()
    {
        if ($this->getRequest()->isPost()) {

            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_redirect('conturifurnizori/inregistrare/');
        }

        return;
    }

    public function removeSpecialCharacters($string = false)
    {
        if ($string) {
            $string = str_replace(array('_'), '-', $string);
            $string = str_replace(array('[\', \']'), '', $string);
            $string = preg_replace('/\[.*\]/U', '', $string);
            $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
            //$string = htmlentities($string, ENT_COMPAT, 'utf-8');
            $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
            $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
            $string = strtolower(trim($string, '-'));
        }

        return $string;
    }

    public function moveUploadedFile($path, $file, $newFileName)
    {
        if (file_exists($path . $file)) {
            // define some initial data
            $fileArr = explode('.', $file);
            $ext = end($fileArr);
            // create final directori structure
            $finalDirectory = Mage::getBaseDir('media') . DS . 'customer' . DS;
            if (!is_dir($finalDirectory)) {
                mkdir($finalDirectory, 0777);
            }
            // continue
            $finalDirectory .= $newFileName[0] . DS;
            if (!is_dir($finalDirectory)) {
                mkdir($finalDirectory, 0777);
            }
            // continue
            $finalDirectory .= $newFileName[1] . DS;
            if (!is_dir($finalDirectory)) {
                mkdir($finalDirectory, 0777);
            }

            // start moving image
            $_image = new Varien_Image($path . $file);
            $_image->constrainOnly(true);
            $_image->keepAspectRatio(true);
            $_image->keepFrame(true);
            $_image->keepTransparency(true);
            //$_image->resize($width, $height);
            $_image->save($finalDirectory . $newFileName . '.' . $ext);
            // delete temp directory
            unlink($path . $file);
            // return new file name
            return DS . $newFileName[0] . DS . $newFileName[1] . DS . $newFileName . '.' . $ext;

        } else {

            return false;
        }
    }
    
}
