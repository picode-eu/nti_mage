<?php

/**
*   Retur URL sau Silent URL: SITE/ep/initialize/ipn
*/

class EuroPayment_EuPlatesc_InitializeController extends Mage_Core_Controller_Front_Action
{
    protected $_callbackAction = false;
    
    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton with euplatesc strandard order transaction information
     *
     * @return EuroPayment_EuPlatesc_Model_Initialize
     */
    public function getInitialize()
    {
        return Mage::getSingleton('ep/initialize');
    }

    /**
     * When a customer chooses EuPlatesc on Checkout/Payment page
     *
     */
    public function paymentAction()
    {
  	
        $this->loadLayout();		
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('ep/initialize_payment'));
        $this->renderLayout();

    }

    public function ipnAction()
    {
        Mage::log('entered ipnAction()', false, 'euPlatesc.log');
        $payment = Mage::getModel('ep/initialize');
        $key = $payment->getConfigData('key');

        if(isset($_POST['invoice_id'])) {

            $zcrsp =  array (
                        'amount'     => addslashes(trim(@$_POST['amount'])),  //original amount
                        'curr'       => addslashes(trim(@$_POST['curr'])),    //original currency
                        'invoice_id' => addslashes(trim(@$_POST['invoice_id'])),//original invoice id
                        'ep_id'      => addslashes(trim(@$_POST['ep_id'])), //Euplatesc.ro unique id
                        'merch_id'   => addslashes(trim(@$_POST['merch_id'])), //your merchant id
                        'action'     => addslashes(trim(@$_POST['action'])), // if action == 0 transaction ok
                        'message'    => addslashes(trim(@$_POST['message'])),// transaction responce message
                        'approval'   => addslashes(trim(@$_POST['approval'])),// if action! = 0 empty
                        'timestamp'  => addslashes(trim(@$_POST['timestamp'])),// meesage timestamp
                        'nonce'      => addslashes(trim(@$_POST['nonce'])),
                        );
             
            $zcrsp['fp_hash'] = strtoupper($this->euplatesc_mac($zcrsp, $key));

            $fp_hash = addslashes(trim(@$_POST['fp_hash']));
            
            if ($zcrsp['fp_hash'] === $fp_hash)  {
                // start facem update in baza de date
                $id = $_POST['invoice_id'];
                Mage::log('increment id from ipnAction = ' . $id, false, 'euPlatesc.log');
                $order = Mage::getModel('sales/order')->loadByIncrementId($id);

                if (!$order->getId()) {
                    //echo "Eroare"; exit;
                }

                if ($zcrsp['action'] == "0") {
                    //echo "Successfully completed";
                    $paidAmount = $_POST['amount'];
                    Mage::log('paid amount = ' . $paidAmount, false, 'euPlatesc.log');
                    
                    // $order->setData('state', "complete");
                    // $order->setStatus("complete"); 
                    
                    if ($order->getTotalPaid() == 0 && $order->canInvoice() && $order->getGrandTotal() == $paidAmount) {
                        //... 
                        $invoice = $order->prepareInvoice();
                        $invoice->register()->capture();
                        //...
                        Mage::getModel('core/resource_transaction')
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder())
                            ->save();
                            
                        $invoice->sendEmail(true, '');
                        
                        /**
                         * set customer account status to active
                         * set EuPlatesc response to order
                         */
                        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                        $customer->setFurnizorAccountStatus(1)->save(); // 1 si for active
                        $epResponse = 'Order completed by using euPlatesc gateway. Auth code: ' . $zcrsp['approval'];
                
                    }
                    
                    
                    // /**
                     // * set customer account status to active
                     // * set EuPlatesc response to order
                     // */
                    // $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                    // $customer->setFurnizorAccountStatus(1)->save(); // 1 si for active
                    // $epResponse = 'Order completed by using euPlatesc gateway. Auth code: ' . $zcrsp['approval'];
                }else {
                    echo "Tranzaction failed" . $zcrsp['message'];
                    //$order->setData('state', "canceled"); // the order has to remain as pending // customized by np
                    //$order->setStatus("canceled");  // the order has to remain as pending // customized by np
                    $epResponse = 'Transaction failed by using euplatesc gateway. Reason: ' . strtolower($zcrsp['message']);
                }

                $history = $order->addStatusHistoryComment($epResponse, false);
                $history->setIsCustomerNotified(false);
                $order->save();
                // end facem update in baza de date
            } else {
                echo "Invalid signature";
            }
   
        } else {
            exit;
        }

    }
    
    /**
     * When a customer cancels payment from euplatesc.
     */
    public function old_cancelAction()
    {
    	/*
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getEPStandardQuoteId(true));
        */
        $this->_redirect('checkout/cart');
    }

    /**
     * when euplatesc returns
     * The order information at this point is in POST
     * variables.  However, you don't want to "process" the order until you
     * get validation from the IPN.
     */
    public function old_successAction()
    {
    	/*
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getEPStandardQuoteId(true));
		*/
        
        $order = Mage::getModel('sales/order');
        $payment = Mage::getModel('epsro/initialize');
        
        //
        // Load the order number
        if (Mage::getSingleton('checkout/session')->getLastOrderId()) {
          $order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
        } else {
          if (isset($_REQUEST["orderid"])) {
            $order->loadByIncrementId((int)$_REQUEST["orderid"]);
          } else {
            echo "<h1>An error occured!</h1>";
            echo "No orderid was supplied to the system!";
            exit();
          }
        }
        
        //
        // Validate the order
        if($order->getId()){
	
        } else {
          echo "<h1>An error occured!</h1>";
          echo "The order id was not known to the system";
          exit();
        }
        
        
        if (!isset($_REQUEST["amount"])) {
            echo "<h1>An error occured!</h1>";
            echo "No amount supplied to the system!";
            exit();
        }
        
        //
        // Validate amount
        if ((int)$_REQUEST["amount"] != (int)($order->getTotalDue()))
        {
          echo "<h1>An error occured!</h1>";
          echo "The amount received from epsro did not match the order amount!";
          exit();
        }
        
        if (!isset($_REQUEST["cur"])) {
            echo "<h1>An error occured!</h1>";
            echo "No currency (cur) supplied to the system!";
            exit();
        }
        
        //
        // validate md5 if enabled
        if (((int)$payment->getConfigData('md5type')) != 0) {
            $tempstr =  ($order->getTotalDue()) .
              $order->getRealOrderId() .
              $_REQUEST["tid"] .
              $payment->getConfigData('md5key');
              
            //
            // Validate currency
            if (!isset($_REQUEST["eKey"])) {
              echo "<h1>An error occured!</h1>";
              echo "No eKey supplied to the system!";
              exit();
            }
            
            if (md5($tempstr) != $_REQUEST["eKey"]) {
              echo "<h1>An error occured!</h1>";
              echo "The MD5 key does not match!";
              exit();
            }
        }
        
        //
        // Everything ok - now add the transaction id to the comments
        $msg = $this->__("Tranzactie prin EuPlatesc.ro, cod unic :") . " <b>" . $_REQUEST["tid"] . "</b>";
        $order->addStatusToHistory($order->getStatus(),$msg);
        $order->save();
                                
        // If not callback - redirect the user to the success page
        if (!$this->_callbackAction) {
          $this->_redirect('checkout/onepage/success');
        } else {
          // Callback - just responsd ok
          echo "OK";
          exit();
        }
    }

    public function old_callbackAction()
    {
      $this->_callbackAction = true;
      $this->successAction();
    }
    
	public function old_addMessageToOrder($message, $order)
	{
        $order->addStatusToHistory($order->getStatus(),$message);
        $order->save();
	}    

  private function hmacsha1($key,$data) {
     $blocksize = 64;
     $hashfunc  = 'md5';
     
     if(strlen($key) > $blocksize)
       $key = pack('H*', $hashfunc($key));
     
     $key  = str_pad($key, $blocksize, chr(0x00));
     $ipad = str_repeat(chr(0x36), $blocksize);
     $opad = str_repeat(chr(0x5c), $blocksize);
     
     $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
     return bin2hex($hmac);
  }
  
  // ===========================================================================================
  private function euplatesc_mac($data, $key = NULL)
  {
    $str = NULL;

    foreach($data as $d)
    {
      if($d === NULL || strlen($d) == 0)
        $str .= '-'; // valorile nule sunt inlocuite cu -
      else
        $str .= strlen($d) . $d;
    }
       
    $key = pack('H*', $key); 

    return $this->hmacsha1($key, $str);
  }

}
