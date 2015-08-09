<?php   
class Picode_ConturiFurnizori_Block_Conturi_Inregistrare extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * Define provider register steps
     * 
     * @return array
     */
    private $_steps = array(
                'personal-info',
                'business-info',
                'billing-info',
                'payment-info',
                'review-info',
            );
    
    /**
     * Define allawed register form inputs
     * 
     * @return array
     */
    private $_allowedInputs = array(
                // personal info
                'customer_group_id',
                'customer_firstname',
                'customer_lastname',
                'customer_email',
                'password_hash',
                // business info
                'business_images_logo',
                'business_descriptions_title',
                'furnizor_company_name',
                'furnizor_company_type',
                'furnizor_company_services',
                'business_descriptions_exp',
                'business_descriptions_desc',
                'furnizor_location_address',
                'furnizor_location_number',
                'furnizor_contact_firstname',
                'furnizor_contact_lastname',
                'furnizor_contact_email',
                'furnizor_contact_phone',
                'furnizor_location_city',
                'furnizor_location_province',
                // billing info
                'address_type',
                'save_in_address_book',
                'email',
                'country_id',
                //'billing_tip',
                'company',
                'billing_tip',
                'billing_cui',
                'billing_nrc',
                'billing_banca',
                'billing_iban',
                'firstname',
                'lastname',
                'billing_ci',
                'billing_cnp',
                'street',
                'street_other',
                'city',
                'region_id',
                'region',
                'postcode',
                'telephone',
                // payment info
                'payment_method',
            );
            
    /**
     * Define unrequired register form inputs
     * 
     * @return array
     */
    private $_unrequiredInputs = array(
                // personal info
                
                // business info
                'business_descriptions_desc',
                'furnizor_location_address',
                'furnizor_location_number',
                'furnizor_contact_firstname',
                'furnizor_contact_lastname',
                'furnizor_contact_phone',
                // billing info
                'billing_iban',
                'billing_cnp',
                'billing_ci',
                'street_other',
                'postcode',
                'telephone',
                // payment info
                
            );
    
	public function __construct()
	{
	    /**
         * Remove billing and payment steps
         * if the account type is a free one
         * 
         * @return array
         */
         
		if (!$this->accountIsPaid() || $this->getQuote()->getGrandTotal() == 0) {
		    foreach ($this->_steps as $key => $step) {
		        if ($step == 'billing-info' || $step == 'payment-info') {
		            $this->_steps = array(
                        'personal-info',
                        'business-info',
                        'review-info',
                    );
                    //unset($this->_steps[$key]);
		        }
		    }
		}
        
        //Zend_Debug::dump($this->_steps); die();
	}
    
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
	
    /**
     * get register forms url
     * 
     * @return string
     */
     
    public function getAjaxUrl()
    {
        return Mage::getUrl('conturifurnizori/inregistrare/processAjaxData/', array('_secure' => true));
    }
    
    public function getRegisterActionUrl()
    {
        return Mage::getUrl('conturifurnizori/inregistrare/saveAccount/', array('_secure' => true));
		//return Mage::getUrl('checkout/onepage/saveOrder');
    }
	
	public function getSuccessUrl()
	{
        return Mage::getUrl('conturifurnizori/inregistrare/succes/', array('_secure' => true));
	}
	
	public function getFailureUrl()
	{
		return Mage::getUrl('conturifurnizori/inregistrare/eroare/', array('_secure' => true));
	}
    
    public function getLoginUrl()
    {
        return Mage::getUrl('customer/account/login/', array('_secure' => true));
    }
    
    public function getResetPasswordUrl()
    {
        return Mage::getUrl('customer/account/forgotpassword/', array('_secure' => true));
    }
    
	/**
     * Set processing methods
     * 
     * @return string (next step id or error message)
     */
	public function processAjaxData()
	{
		$quote = $this->getQuote();
		
		if (!$quote->hasItems() || $quote->getHasError()) {
		    $this->_redirect('conturifurnizori/produse/list/');
		    return;
		}
		
	    if ($data = $this->getRequest()->getPost()) {
            
            switch ($data['current_step']) {
                case 'personal-info':
                    // process personal information
                    $result = $this->processPersonalInfo($quote, $data);
                    break;
                case 'business-info':
                    // process business information
                    $result = $this->processBusinessInfo($quote, $data);
                    break;
                case 'billing-info':
                    // process billing information
                    $result = $this->processBillingInfo($quote, $data);
                    break;
                case 'payment-info':
                    // process payment information
                    $result = $this->processPaymentInfo($quote, $data);
                    break;
                default:
                    $result = 'error~Ceva neasteptat s-a intamplat. Te rugam sa incerci mai tarziu.';
            }
            
            return $result;
            
	    } else {
	        return 'error~Ceva neasteptat s-a intamplat. Te rugam sa incerci mai tarziu.';
	    }
        
        return;
        
	}
    
    /**
     * Processes personal info form
     */
    public function processPersonalInfo($quote, $data)
    {
        // check if customer exists
    	if (Mage::getModel('customer/customer')->loadByEmail($data['customer_email'])->getId()) {
    		return 'error~Exista deja un cont creat cu adresa de email: ' . $data['customer_email'] . '.<br /><a href="' . $this->getLoginUrl() . '">Autentifica-te</a> sau solicita <a href="' . $this->getResetPasswordUrl() . '">resetarea parolei</a> daca nu ti-o mai amintesti.';
    	}
		
        foreach ($data as $name => $value) {
            // check against empty data
            if (!isset($value) || empty($value)) {
                return 'error~Campurile marcate cu * sunt obligatorii.';
            } else {
                if (in_array($name, $this->_allowedInputs)){
                    if ($name == 'password_hash') {
                        if ($value != $data['confirmation']) {
                            return 'error~Confirmarea parolei nu este identica cu parola furnizata.';
                        } else {
                            $quote->setData($name, md5(trim($value)));
                        }
                    } else {
                        $quote->setData($name, trim(strip_tags($value)));
                    }
                }
            }
        }
        
        $quote->setCheckoutMethod('register');
        $quote->setCustomerGroupId(1);
        $result = $this->saveUpdatedQuote($quote);
        return $result;
    }
    
    /**
     * Processes business info form
     */
    public function processBusinessInfo($quote, $data)
    {
        foreach ($data as $name => $value) {
            // check against empty data
            if ((!isset($value) || empty($value)) && !in_array($name, $this->_unrequiredInputs)) {
                return 'error~Campurile marcate cu * sunt obligatorii.';
            } else {
                if (in_array($name, $this->_allowedInputs)){
                    $quote->setData($name, trim(strip_tags($value)));
                }
            }
        }
        
        $result = $this->saveUpdatedQuote($quote);
        //Zend_Debug::dump($quote->getData());
        return $result;
    }
    
    /**
     * Processes billing info form
     */
    public function processBillingInfo($quote, $data)
    {
        //set default email address
        $data['email'] = $quote->getCustomerEmail();
        $quote = $this->getAddress('billing');
        
        foreach ($data as $name => $value) {
            // check against empty data
            if ((!isset($value) || empty($value)) && !in_array($name, $this->_unrequiredInputs)) {
                return 'error~Campurile marcate cu * sunt obligatorii.';
            } else {
                if (in_array($name, $this->_allowedInputs)){
                    $quote->setData($name, trim(strip_tags($value)));
                }
            }
        }
        
        if ($this->getCustomer()) {
            $quote->setData('checkout_method', 'update');
        }
        
        $result = $this->saveUpdatedQuote($quote);
        return $result;
    }
    
    /**
     * Get regions for specific country
     * 
     * @return array
     */
    public function getRegions($countryId)
    {
        $regionCollection = Mage::getModel('directory/region')
                    ->getResourceCollection()
                    ->addCountryFilter($countryId)
                    ->load();
                    
        if ($regionCollection) {
            return $regionCollection;
        } else {
            return false;
        }
    }
    
    /**
     * Processes payment info form
     */
    public function processPaymentInfo($quote, $data)
    {
        //Zend_Debug::dump($data); die();
        if (!isset($data['payment_method'])) {
            return 'error~Alege o metoda de plata.';
        }
        
        $quote = $quote->getPayment();
        foreach ($data as $name => $method) {
            // check against empty data
            if (empty($method)) {
                return 'error~Ceva neasteptat s-a intamplat. Te rugam sa incerci mai tarziu.';
            } else {
                if (in_array($name, $this->_allowedInputs)){
                    $quote->setMethod($method);
                }
            }
        }
        
        $result = $this->saveUpdatedQuote($quote);
        return $result;
    }
    
    /**
     * Update current quote
     */
    public function saveUpdatedQuote($quote)
    {
        if ($quote) {
            //Zend_Debug::dump($quote->getData()); die();
            try
            {
                $quote->save();
                $currentStep = $this->getRequest()->getParam('current_step');
                $currentStep = array_search($currentStep, $this->_steps);
                $nextStep = $currentStep + 1;
                $result = $this->_steps[$nextStep];
            }
            catch (Exception $e)
            {
                $result = 'error~Error message: ' . $e->getMessage();
            }

            //Zend_Debug::dump($quote->getData()); die('quote');

            return $result;
        } else {
            return 'error~Ceva neasteptat s-a intamplat. Te rugam sa incerci mai tarziu.';
        }
    }
	
    /**
     * Check account type
     * 
     * @return bool
     */
    public function accountIsPaid()
    {
        $itemsCollection = $this->getQuote()->getItemsCollection();
        // iterate through items collection
        foreach ($itemsCollection as $item) {
            // check if account type from cart is a paid one
            $itemDetails = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
            
            if ($itemDetails->getContTip() == 'platit') {
                return true;
            } else {
                return false;
            }
        }
        
        return;
    }
    
    /**
     * Retrive quote address details
     * 
     * @return object
     */
    public function getAddress($type)
    {
        $quote = $this->getQuote();
        $addressesCollection = $quote->getAddressesCollection();
        
        foreach ($addressesCollection as $address) {
            if ($address['address_type'] == $type) {
                return $address;
            } else {
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * Retrieve available payment methods
     *
     * @return array
     */
    public function getActivPaymentMethods()
    {
        $methods = '';
        $quote = $this->getQuote();
        $store = $quote ? $quote->getStoreId() : null;
        
        foreach (Mage::helper('payment')->getStoreMethods($store, $quote) as $method) {
            // if ($this->_canUseMethod($method) && $method->isApplicableToQuote(
                // $quote,
                // Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL
            // )) {
                $methods[] = $method;
            // }
        }
        
        return $methods;
    }
    
    /**
     * Check payment method model
     *
     * @param Mage_Payment_Model_Method_Abstract $method
     * @return bool
     */
    protected function _canUseMethod($method)
    {
        return $method->isApplicableToQuote($this->getQuote(), Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_COUNTRY
            | Mage_Payment_Model_Method_Abstract::CHECK_USE_FOR_CURRENCY
            | Mage_Payment_Model_Method_Abstract::CHECK_ORDER_TOTAL_MIN_MAX
        );
    }
    
    public function getLastIncrementIdFromSession()
    {
        return Mage::getSingleton('customer/session')->getLastIncrementId();
    }

    public function getLogoUploadUrl()
    {
        return Mage::getUrl('conturifurnizori/inregistrare/logoupload', array('_secure' => true));
    }

    function ajaxUploadImage()
    {
        $form = key($_FILES);
        $name = $_FILES[$form]['name'];
        $size = $_FILES[$form]['size'];

        if(strlen($name)) {
            // initial settings
            //$customerId = $this->getCustomer()->getId();
            $validFormats = array('jpg', 'png', 'bmp');
            $tmpUploadDir = 'tmp_uploads';
            $maxOriginalSize = 5120 * 1024;
            // define final image sizes
            switch ($form) {
//                case 'coverimage':
//                    $inputName   = 'coperta';
//                    $finalWidth  = 1024;
//                    $finalHeight = 353;
//                    break;
                case 'logoimage':
                    $inputName   = 'logo';
                    $finalWidth  = 512;
                    $finalHeight = 285;
                    break;
                default:
                    return 'Something went wrong in the form';
                    break;
            }
            // define final image quality
            $quality = 100;

            // create temporary upload directory
            $path = Mage::getBaseDir('media') .  DS . $tmpUploadDir . DS;
            if (!is_dir($path)) mkdir($path);
            // create temporary customer directory
            $path .= 'tmp_logos' . DS;
            if (!is_dir($path)) mkdir($path);
            // get file name and size
            $name = $_FILES[$form]['name'];
            $size = $_FILES[$form]['size'];
            $type = $_FILES[$form]['type'];
            // extract file extension from name
            $nameArr = explode('.', $name);
            $ext     = end($nameArr);

            if(in_array(strtolower($ext), $validFormats)) { // validate file format

                if($size < $maxOriginalSize) { // check file size

                    $tmpFile = $_FILES[$form]['tmp_name'];

                    if(file_exists($tmpFile)){ // check uploaded file

                        switch(strtolower($type))
                        {
                            case 'image/jpg':
                            case 'image/jpeg':
                            case 'image/pjpeg':
                                $image = imagecreatefromjpeg($tmpFile);
                                break;
                            case 'image/png':
                                $image =  imagecreatefrompng($tmpFile);
                                break;
                            // case 'image/gif':
                            // $image =  imagecreatefromgif($tmpFile);
                            // break;
                            // case 'bmp':
                                // $image = ImageCreateFromwbmp($tmpFile);
                                // break;
                            default:
                                return 'Invalid file format'; //output error and exit
                        }

                        $newName = time() . '-' . $this->removeSpecialCharacters(reset($nameArr));
                        $fianlImageDest = $path . $newName . '.' . $ext;
                        list($currWidth, $currHeight) = getimagesize($tmpFile);

                        // let's resize / crop the uploaded image
                        if ($this->cropOrResizeImage($image, $quality, $currWidth, $currHeight, $finalWidth, $finalHeight, $fianlImageDest))
                        {
                            //list($resizedWidth, $resizedHeight) = getimagesize($fianlImageDest);
                            $tmpMediaDir  = Mage::getBaseUrl('media') . $tmpUploadDir . '/' . 'tmp_logos' . '/';
                            $response  = '<img src="' . $tmpMediaDir . $newName . '.' . $ext . '" class="' . $form . '-preview">';
                            $response .= '<input type="hidden" name="business_images_' . $inputName . '" value="' . $newName . '.' . $ext . '" />';
                            return $response;

                        } else {

                            return '<div class="error">Imaginea nu a fost uploadata!</div>';
                        }

                    } else {

                        return '<div class="error">Ceva neasteptat s-a intamplat. Te rugam sa incerci din nou.</div>';
                    }
                } else {

                    return '<div class="error">Imaginea e prea mare! (maxim permis 1MB)</div>';
                }
            } else {

                return '<div class="error">Format imagine nepermis! (fisiere permise: jpg, png sau bmp)</div>';
            }

        } else {

            return '<div class="error">Selecteaza o imagine.</div>';
        }

        return;
    }

    public function cropOrResizeImage($image, $quality, $currWidth, $currHeight, $finalWidth, $finalHeight, $fianlImageDest)
    {
        if ($image) {
            // check if ratios match
            $_ratio = array($currWidth / $currHeight, $finalWidth / $finalHeight);

            if ($_ratio[0] !=  $_ratio[1]) { // crop image
                // find the right scale to use
                $_scale = min((float)($currWidth / $finalWidth),(float)($currHeight / $finalHeight));

                // coords to crop
                $cropX = (float)($currWidth - ($_scale * $finalWidth));
                $cropY = (float)($currHeight - ($_scale * $finalHeight));

                // cropped image size
                $cropW = (float)($currWidth - $cropX);
                $cropH = (float)($currHeight - $cropY);

                $crop = ImageCreateTrueColor($cropW, $cropH);
                // crop the middle part of the image to fit proportions
                ImageCopy($crop, $image, 0, 0, (int)($cropX / 2), (int)($cropY / 2), $cropW, $cropH);
            }

            // do the thumbnail
            $newThumb = ImageCreateTrueColor($finalWidth, $finalHeight);
            if (isset($crop)) { // been cropped
                ImageCopyResampled($newThumb, $crop, 0, 0, 0, 0, $finalWidth, $finalHeight, $cropW, $cropH );
                ImageDestroy($crop);
            } else { // ratio match, regular resize
                ImageCopyResampled($newThumb, $image, 0, 0, 0, 0, $finalWidth, $finalHeight, $currWidth, $currHeight );
            }

            ImageJpeg($newThumb, $fianlImageDest, $quality);
            ImageDestroy($newThumb);
            ImageDestroy($image);

            return true;
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
}

/*

$quote = array(52) {
  ["entity_id"] => string(1) "9"
  ["store_id"] => string(1) "1"
  ["created_at"] => string(19) "2014-06-18 09:41:20"
  ["updated_at"] => string(19) "2014-06-18 09:41:21"
  ["converted_at"] => NULL
  ["is_active"] => string(1) "1"
  ["is_virtual"] => string(1) "0"
  ["is_multi_shipping"] => string(1) "0"
  ["items_count"] => string(1) "1"
  ["items_qty"] => string(6) "1.0000"
  ["orig_order_id"] => string(1) "0"
  ["store_to_base_rate"] => string(6) "1.0000"
  ["store_to_quote_rate"] => string(6) "1.0000"
  ["base_currency_code"] => string(3) "RON"
  ["store_currency_code"] => string(3) "RON"
  ["quote_currency_code"] => string(3) "RON"
  ["grand_total"] => string(6) "0.0000"
  ["base_grand_total"] => string(6) "0.0000"
  ["checkout_method"] => NULL
  ["customer_id"] => NULL
  ["customer_tax_class_id"] => string(1) "3"
  ["customer_group_id"] => string(1) "0"
  ["customer_email"] => NULL
  ["customer_prefix"] => NULL
  ["customer_firstname"] => NULL
  ["customer_middlename"] => NULL
  ["customer_lastname"] => NULL
  ["customer_suffix"] => NULL
  ["customer_dob"] => NULL
  ["customer_note"] => NULL
  ["customer_note_notify"] => string(1) "1"
  ["customer_is_guest"] => string(1) "0"
  ["remote_ip"] => string(14) "86.124.102.230"
  ["applied_rule_ids"] => NULL
  ["reserved_order_id"] => NULL
  ["password_hash"] => NULL
  ["coupon_code"] => NULL
  ["global_currency_code"] => string(3) "RON"
  ["base_to_global_rate"] => string(6) "1.0000"
  ["base_to_quote_rate"] => string(6) "1.0000"
  ["customer_taxvat"] => NULL
  ["customer_gender"] => NULL
  ["subtotal"] => string(6) "0.0000"
  ["base_subtotal"] => string(6) "0.0000"
  ["subtotal_with_discount"] => string(6) "0.0000"
  ["base_subtotal_with_discount"] => string(6) "0.0000"
  ["is_changed"] => string(1) "1"
  ["trigger_recollect"] => string(1) "0"
  ["ext_shipping_info"] => NULL
  ["gift_message_id"] => NULL
  ["is_persistent"] => string(1) "0"
  ["x_forwarded_for"] => string(14) "86.124.102.230"
}

*/