<?php
/**
 * EuPlatesc
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * available at http://opensource.org/licenses/gpl-3.0.html
 *
 * @category   EuPlatesc
 * @package    EuroPayment_EuPlatesc
 * @copyright  Copyright (c) 2011 EuroPayment Services - http://www.euplatesc.ro 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @author     euplatesc.ro - 2011
 */
class EuroPayment_EuPlatesc_Model_Initialize extends Mage_Payment_Model_Method_Abstract 
{
	protected $_code  = 'ep_initialize';
	protected $_allowCurrencyCode = array('RON', 'EUR', 'USD');
	protected $_formBlockType = 'ep/initialize_form';
	
	/**
	 * This payment method uses capture
	 *
	 * @return true
	 */
    public function canCapture()
    {
        return true;
    }

    /**
     * Using internal pages for input payment data
     *
     * @return false
     */
    public function canUseInternal()
    {
        return false;
    }
    
    /**
     * Using for multiple shipping address
     *
     * @return false
     */
    public function canUseForMultishipping()
    {
        return true;
    }

    /**
     * Create the form block
     *
     * @param string $name
     */
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('ep/initialize_form', $name)
            ->setMethod('ep_initialize')
            ->setPayment($this->getPayment())
            ->setTemplate('ep/initialize/form.phtml');

        return $block;
    }
    
    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

	
	/**
     * Get one line item key-value array
     *
     * @param Mage_Core_Model_Abstract $salesEntity
     * @param Varien_Object $item
     * @return array
     */
    protected function _prepareLineItemFields(Mage_Core_Model_Abstract $salesEntity, Varien_Object $item)
    {
        if ($salesEntity instanceof Mage_Sales_Model_Order) {
            $qty = $item->getQtyOrdered();
            $amount = $item->getBasePrice();
            // TODO: nominal item for order
        } else {
            $qty = $item->getTotalQty();
            $amount = $item->isNominal() ? 0 : $item->getBaseCalculationPrice();
        }
        // workaround in case if item subtotal precision is not compatible with PayPal (.2)
        $subAggregatedLabel = '';
        if ((float)$amount - round((float)$amount, 2)) {
            $amount = $amount * $qty;
            $subAggregatedLabel = ' x' . $qty;
            $qty = 1;
        }
        return array(
            'id'     => $item->getSku(),
            'name'   => $item->getName() . $subAggregatedLabel,
            'qty'    => $qty,
            'amount' => (float)$amount,
        );
    }

	
	
    /**
     * Create an array with all data required by euplatesc
     *
     * @return array
     */
	public function getCheckoutFormFields()
	{
		/**********************
		epsro: curr, invoice_id, order_desc, merch_id, timestamp, nonce, fp_hash 
		epsro: sunt obligatorii
		epsro: restul sunt campuri necesare pentru verificari antifrauda
		epsro: fname, lname, email, phone sunt importante
		***********************/
		
		/*paypal standard*/
		/*
		$orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $api = Mage::getModel('paypal/api_standard')->setConfigObject($this->getConfig());
        $api->setOrderId($orderIncrementId)
            ->setCurrencyCode($order->getBaseCurrencyCode())
            //->setPaymentAction()
            ->setNotifyUrl(Mage::getUrl('paypal/ipn/'))
            ->setReturnUrl(Mage::getUrl('paypal/standard/success'))
            ->setCancelUrl(Mage::getUrl('paypal/standard/cancel'));

        // export address
        $isOrderVirtual = $order->getIsVirtual();
        $address = $isOrderVirtual ? $order->getBillingAddress() : $order->getShippingAddress();
        if ($isOrderVirtual) {
            $api->setNoShipping(true);
        }
        elseif ($address->getEmail()) {
            $api->setAddress($address);
        }

        list($items, $totals, $discountAmount, $shippingAmount) = Mage::helper('paypal')->prepareLineItems($order, false, true);
        // prepare line items if required in config
        if ($this->_config->lineItemsEnabled) {
            $api->setLineItems($items)->setLineItemTotals($totals)->setDiscountAmount($discountAmount);
        }
        // or values specific for aggregated order
        else {
            $grandTotal = $order->getBaseGrandTotal();
            if (!$isOrderVirtual) {
                $api->setShippingAmount($shippingAmount);
                $grandTotal -= $shippingAmount;
            }
            $api->setAmount($grandTotal)->setCartSummary($this->_getAggregatedCartSummary());
        }

        $result = $api->getStandardCheckoutRequest();
        return $result;
		*/
		/*end paypal*/
		
		
		
		
		
		$orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		
		/*
		$items = array();
        foreach ($order->getAllItems() as $item) {
             if (!$item->getParentItem() ) {
                $items[] = new Varien_Object($this->_prepareLineItemFields($order, $item));
             }
        }
		
		foreach ($items as $item) {
			$items_to_string = $item->getName().' | Cod: '. $item->getId().' | Cantitate: '.$item->getQty.' | Valoare: '.$item->getAmount().'<br>';
		}
		*/
		
		$customer = $this->getCustomer();
        $address = $order->getShippingAddress();
        $amount = $order->getBaseGrandTotal();

		$billing	= $order->getBillingAddress();
		$billingId	= (int)$billing->getCustomerAddressId();
		
		$shipping = $order->getShippingAddress();
		

		$sArr = array(
			'amount'		=> number_format($amount, 2, '.', ''),
			'curr'			=> $order->getBaseCurrencyCode(),
			'invoice_id'	=> $orderIncrementId,
			'order_desc'	=> "Comanda online www.euplatesc.ro <br>".$items_to_string,
			'merch_id'		=> $this->getConfigData('merchant'),
			'timestamp'		=> gmdate("YmdHis"),
//			'lang'			=> substr(Mage::app()->getLocale()->getLocaleCode(),0.2),
			'nonce'			=> md5(microtime() . mt_rand()),
//			'PAY_METHOD'	=> $this->getConfigData('acceptedcc'),
			'fname'			=> $billing->getFirstname(),
			'lname'			=> $billing->getLastname()
		);
		
//		if ('' != $billing->getIdCardSeries())			$sArr['BILL_CISERIAL']		= $billing->getIdCardSeries();
//		if ('' != $billing->getIdCardNumber())			$sArr['BILL_CINUMBER']		= $billing->getIdCardNumber();
//		if ('' != $billing->getIdCardIssuer())			$sArr['BILL_CIISSUER']		= $billing->getIdCardIssuer();
//		if ('' != $billing->getCompanyFiscalCode())		$sArr['BILL_CNP']			= $billing->getCompanyFiscalCode();
		if ('' != $billing->getCompany())				$sArr['company']			= $billing->getCompany();
//		if ('' != $billing->getCompanyFiscalCode())		$sArr['BILL_FISCALCODE']	= $billing->getCompanyFiscalCode();
//		if ('' != $billing->getCompanyCommercialReg())	$sArr['BILL_REGNUMBER']		= $billing->getCompanyCommercialReg();
//		if ('' != $billing->getCompanyBank())			$sArr['BILL_BANK']			= $billing->getCompanyBank();
//		if ('' != $billing->getCompanyIban())			$sArr['BILL_BANKACCOUNT']	= $billing->getCompanyIban();

		$sArr['email']			= ('' == $billing->getEmail()) ? $customer->getEmail() : $billing->getEmail();
		$sArr['phone']			= $billing->getTelephone();
		$sArr['fax']			= $billing->getFax();
		$sArr['add']			= $billing->getStreet(1)." ".$billing->getStreet(2);
//		$sArr['BILL_ADDRESS2']			= $billing->getStreet(2);
		$sArr['zip']			= $billing->getPostcode();
		$sArr['city']			= $this->clean4euplatesc($billing->getCity());
		$sArr['state']			= $this->clean4euplatesc($billing->getRegion());
		$sArr['country']		= $billing->getCountry();
			
		$sArr['sfname']			= $shipping->getFirstname();
		$sArr['slname']			= $shipping->getLastname();
		$sArr['scompany']		= $shipping->getCompany();
		$sArr['sphone']			= $shipping->getTelephone();
		$sArr['sadd']			= $shipping->getStreet(1)." ".$shipping->getStreet(2);
//		$sArr['DELIVERY_ADDRESS2']		= $shipping->getStreet(2);
		$sArr['szip']			= $shipping->getPostcode();
		$sArr['scity']			= $this->clean4euplatesc($shipping->getCity());
		$sArr['sstate']			= $this->clean4euplatesc($shipping->getRegion());
		$sArr['scountry']		= $shipping->getCountry();

//		$sArr['ship_prices']	= $shipping->getBaseShippingAmount() + $shipping->getBaseShippingTaxAmount() + $shipping->getShippingiTaxAmount();
	//	$sArr['ship_prices']	= $order->getShippingInclTax();
		
//		$sArr['DESTINATION_CITY']	= $shipping->getCity();
//		$sArr['DESTINATION_STATE']	= $shipping->getRegion();
//		$sArr['DESTINATION_COUNTRY']= $shipping->getCountry();


		/*if ( (bool)$this->getConfigData('test') ) {
			$sArr['TESTORDER'] = 'TRUE';
		}
		if ( (bool)$this->getConfigData('debug') ) {
			$sArr['DEBUG'] = 0;
		}
		
		$items = $order->getAllItems();
		if ($items)
		{
			$ORDER_PNAME = array();
			$ORDER_PCODE = array();
			$ORDER_PRICE = array();
			$ORDER_QTY   = array();
			$ORDER_VAT   = array();
			
			foreach($items as $item)
			{
				if ($item->getParentItem())
				{
					continue;
				}
				array_push($ORDER_PNAME, $this->clean4euplatesc($item->getName()).'<br>Cod:'.$item->getSku()); 
				array_push($ORDER_PCODE, $item->getSku());
				array_push($ORDER_PRICE, sprintf('%.2f', $item->getBasePrice()));
				array_push($ORDER_QTY, $item->getQtyOrdered());
				array_push($ORDER_VAT, $item->getBasePriceInclTax()-$item->getBasePrice());
				//array_push($ORDER_VAT, $item->getTaxAmount()); //valoare TVA total
			}

			$sArr = array_merge($sArr, array('orders' => $ORDER_PNAME));
			//$sArr = array_merge($sArr, array('ORDER_PCODE' => $ORDER_PCODE));
			$sArr = array_merge($sArr, array('prices' => $ORDER_PRICE));
			$sArr = array_merge($sArr, array('qty'   => $ORDER_QTY));
			$sArr = array_merge($sArr, array('vats'   => $ORDER_VAT));
		}  */

			$sArr = array_merge($sArr, array('fp_hash' => $this->hmac($this->getHmacString($sArr))));

		// $sReq = '';
		$rArr = array();
		foreach ($sArr as $k => $v)
		{
			/*
			replacing & char with and. otherwise it will break the post
			*/
			$value =  str_replace("&","and",$v);
			$rArr[$k] =  $value;
			// $sReq .= '&'.$k.'='.$value;
		}
		
		try {
			$order->sendNewOrderEmail();
		} catch (Exception $ex) {  }
		
		return $rArr;
	}
    
    /**
     * Get customer object
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }
    
    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Check if we should submit the form in a new order
     *
     * @return boolean
     */
    public function getNewwindow()
    {
    	return (bool) $this->getConfigData('newwindow');
    }
    
    /**
     * Get the url to redirect to !
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
    	return Mage::getUrl('ep/initialize/payment');
    }

    /**
     * Get url of the payment 
     *
     * @return string
     */
    public function getUrl()
    {
         return $this->getConfigData('url');
    }
    
    /**
     * Run when order is validated
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return EuroPayment_EuPlatesc_Model_Initialize
     */
    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
    	exit(__FUNCTION__);
       return $this;
    }

    /**
     * Run when invoice is created
     *
     * @param Mage_Sales_Model_Invoice_Payment $payment
     * @return EuroPayment_EuPlatesc_Model_Initialize
     */
    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {
    	exit(__FUNCTION__);
		return $this;
    }

    
    /**
     * sdfnasdjkfgnas
     * 
     * @deprecated 
     */
    public function getEPUrl()
    {
    	echo __FUNCTION__ . " is deprecated.";
    	exit();
    }

	/**
	 * asdfgasdfasfdsafa
	 *
	 * @param unknown_type $str
	 * @deprecated 
	 */
    public function clean4EP($str) {
    	echo __FUNCTION__ . " is deprecated, use 'clean4euplatesc'.";
    	exit();
    }
    
    /**
     * Validate IPN data
     * @uses Code from EuPlatesc.ro example
     *
     * @param array $request
     * @return boolean
     */
	public function validateIpnData(array $request)
	{
		if (3 >= count($request))
		{
			throw new Exception ("Invalid input from server", 1);
		}
		
		if (!array_key_exists('HASH', $request))
		{
			throw new Exception ("Did not receive HASH from server.", 2);
		}
		
		if (3 != count(array_intersect(array('IPN_PID', 'IPN_PNAME', 'IPN_DATE'), array_unique(array_keys($request)))))
		{
			throw new Exception ("Insufficient input from server", 3);
		}

		$result = '';
		$return = '';
		$date_return = date('YmdGis');

		while(list($key, $val) = each($request))
		{
			// $$key=$val; // I don't get the point for this

			/* get values */
			if('HASH' != $key)
			{
				if(is_array($val))
				{
					$result .= $this->expandArray($val);
				}
				else
				{
					$result	.= $this->expandString($val);
				}
			}
		}
		unset($key, $val);
		
		$body = ob_get_contents();
		if (function_exists('apache_request_headers'))
		{
			$body = print_r(apache_request_headers(), true) . "\n\n" . $body;
		}

		$return .= $this->expandString($request["IPN_PID"][0]);
	    $return .= $this->expandString($request["IPN_PNAME"][0]);
		$return .= $this->expandString($request["IPN_DATE"]);
		$return .= $this->expandString($date_return);
		
		$hash = $this->hmac( $result ); /* HASH for data received */
		if ( $hash != $request['HASH'] )
		{
			// Hash verification failed... get extra info...
			$body .= $result
				. "\r\n\r\nHash: " . $hash 
				. "\r\n\r\nSignature: " . $signature
				. "\r\n\r\nReturnSTR: " . $return
				 . "POST\n" . print_r($_POST, true);
			mail("office@euplatesc.ro", "BAD IPN Signature", $body, "");
			
			throw new Exception ("Invalid Hash key", 2);
		}
	
	    /* EuPlatesc response */
	    $result_hash = $this->hmac($return);
		if (3 != count(array_intersect(array('REFNO', 'REFNOEXT', 'ORDERNO'), array_unique(array_keys($request)))))
		{
			throw new Exception ("Missing one of: REFNO / REFNOEXT / ORDERNO", 4);
		}

	    /* Begin automated procedures (START YOUR CODE)*/
		// fetch write database connection that is used in Mage_Core module
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		/* @var $write Zend_Db_Adapter_Abstract */
		
		$mage = (int)$request['REFNOEXT'];
		if ($mage <= 0)
		{
			throw new Exception ("NonNumerical REFNOEXT", 5);
		}
		$order = Mage::getModel('sales/order');
		/* @var $order Mage_Sales_Model_Order */
		$order->loadByIncrementId($mage);
		if ($order->isEmpty())
		{
			throw new Exception ("Unmatched order", 6);
		}

		$epsro = (int)$request['REFNO'];
		if ($epsro <= 0)
		{
			throw new Exception ("NonNumerical REFNO", 7);
		}
		$orderno = (int)$request['ORDERNO'];
		if ($orderno <= 0)
		{
			throw new Exception ("NonNumerical ORDERNO", 8);
		}

		$status = $order->getStatus();
		if($status == Mage_Sales_Model_Order::STATE_PROCESSING
			|| $status == Mage_Sales_Model_Order::STATE_CLOSED
			|| $status == Mage_Sales_Model_Order::STATE_CANCELED
			|| $status == Mage_Sales_Model_Order::STATE_HOLDED)
		throw new Exception ("Order status ($status) is incompatible with IPN.", 9);
		
		// now $write is an instance of Zend_Db_Adapter_Abstract
		try
		{
			$write->query("INSERT INTO euplatesc_map VALUES (" . $order->getId() . ", $mage, $epsro, $orderno)");	
		}
		catch (Exception $e)
		{
			throw new Exception ("Order already processed." . $e->getMessage(), 10);
		}
		
		if(0 != abs($request['IPN_TOTALGENERAL'] - $order->getTotalDue()))
		{
			 $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING, "Price Mismatch: ".$request['IPN_TOTALGENERAL']." vs. ".$order->getTotalDue()." local", 1);
			 $order->save();
			 
			 throw new Exception ("Price Mismatch: ".$request['IPN_TOTALGENERAL']." vs. ".$order->getTotalDue(), 11);
		}

		// OK, now we're sure we have the money, 
		// the order status is fine, so
		// we should put the order in "Processing" state
	 	$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING, 'EuPlatesc.ro confirmation via IPN code: '.$request['REFNO']. PHP_EOL);
		$order->save();

		return "<EPSRO>".$date_return."|".$result_hash."</EPSRO>";
	}

	public function validateIpnHash($request) { exit(__FUNCTION__); }
	public function ipnResponseMessage($request) { exit(__FUNCTION__); }
	public function sendIDN($shipment) { exit(__FUNCTION__); }
	public function getSession() { exit(__FUNCTION__); }


    /**
     * euplatesc doesn't seem to handle corectly all utf-8 characters... so I'll strip them
     *
     * @param string $str
     * @return string
     */
	private function clean4euplatesc($str)
	{
		$from	= array('ş','Ş', 'ţ', 'Ţ', 'Ă', 'ă', 'Â', 'â', 'Î', 'î' );
		$to		= array('s','S', 't', 'T', 'A', 'a', 'A', 'a', 'I', 'i' );
		$str = str_replace($from, $to, $str);
		
		$str = preg_replace('@[^a-z0-9 \-\.\(\)\,]+@iUs', '', $str);
		
		return $str;
	}


/**
 * 
 * 
 * Supplied by EuPlatesc in some examples
 * 
 * 
 */

	/**
	 * Return checksum for EuPlatesc.ro Transaction
	 *
	 * @param array $paymentData
	 * @return string
	 */
    private function getHmacString ($paymentData) {
		$retval = "";
		$retval .= $this->expandString($paymentData['amount']);
		$retval .= $this->expandString($paymentData['curr']);
		$retval .= $this->expandString($paymentData['invoice_id']);
		$retval .= $this->expandString($paymentData['order_desc']);
		$retval .= $this->expandString($paymentData['merch_id']);
		$retval .= $this->expandString($paymentData['timestamp']);
		$retval .= $this->expandString($paymentData['nonce']);
		
		return $retval;
    }

    /**
     * Concatenates <strlen><str> Used to compose EuPlatesc Transaction HMAC string
     *
     * @param string $string
     * @return string
     */
    private function expandString ($string) {
		$size = strlen($string);
		$retval = $size . $string;
		return $retval;
    }


    /**
     * Concatenates <strlen><str> for each string in an array Used to compose EuPlatesc Transaction HMAC string
     *
     * @param array $array
     * @return string
     */
    private function expandArray($array) {
		$retval = "";
		for ($i = 0; $i < count($array); $i++)
		{
			$retval .= $this->expandString($array[$i]);
		}
		return $retval;
    }

    /**
     * Calculates and returns hmac value for the concatenated array string
     *
     * @param string $data
     * @return string
     */
    private function hmac($data) {
		
		$key = $this->getConfigData('key');
                $key = pack('H*', $key); // convertim codul secret intr-un string binar

		$blocksize = 64;
  		$hashfunc  = 'md5';
  
  		if(strlen($key) > $blocksize)
    		$key = pack('H*', $hashfunc($key));
  
	  	$key  = str_pad($key, $blocksize, chr(0x00));
  		$ipad = str_repeat(chr(0x36), $blocksize);
 	 	$opad = str_repeat(chr(0x5c), $blocksize);
  
  		$hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
  		return strtoupper(bin2hex($hmac));

    }

}
