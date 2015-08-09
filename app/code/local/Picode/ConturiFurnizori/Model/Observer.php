<?php
class Picode_ConturiFurnizori_Model_Observer
{
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
    
    public function clearCart($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $quote = $observer->getEvent()->getQuote();
        $cart = $this->_getCart();
        
        if ($product->getTypeId() == 'conturifurnizori' && $cart->getItemsCount()) {
            foreach ($cart->getQuote()->getItemsCollection() as $_item) {
                if ($_item->getId() != $quote->getId() &&  $_item->getProduct()->getTypeId() == 'conturifurnizori') {
                    $_item->isDeleted(true);
                }
            }
        }

        if (Mage::app()->getRequest()->getParam('early_bird')) {
            Mage::getSingleton('customer/session')->setIsEarlyBird(1);
        }
        
        return $this;
    }
    
    public function redirectToCheckout($observer)
    {
        $grandTotal = $observer->getEvent()->getCart()->getQuote()->getGrandTotal();
        
        if ($grandTotal == 0) {
            Mage::app()->getResponse()->setRedirect(Mage::getUrl('conturifurnizori/inregistrare/', array('_secure' => true)));
            return $this;
        }
        
        return $this;
    }
    
    /*
     * redirect customer if he/she belongs to a specific group
    */
    public function afterLogIn(Varien_Event_Observer $observer)
    {
        //Zend_Debug::dump($observer->getEvent()); die();
        
        // $session= Mage::getSingleton('checkout/session');
        // $quote = $session->getQuote();
//         
        // $cart = Mage::getModel('checkout/cart');
        // $cartItems = $cart->getItems();
//         
        // foreach ($cartItems as $item){
            // $quote->removeItem($item->getId())->save();
        // }
        
        return $this;
        
        /* not in use for the momment
        if($observer->getCustomer()->getGroupId() == 4){ // 4 is for furnizori
            $this->_getSession()->setAfterAuthUrl(Mage::getUrl('furnizor/account/', array('_secure' => true,)));
            return;
        }else{
            $this->_getSession()->setAfterAuthUrl(Mage::getUrl('customer/account/', array('_secure' => true,)));
            return;
        }
        */
    }
    
    /*
     * create a new layout handle
     * to be able to easily customize styling of pages or 
     * enable/disable specific blocks for the customer groups
     * 
     * we can use it in local.xml or any other layout file
    */
    public function addCustomerGroupHandle(Varien_Event_Observer $observer)
    {
        if (Mage::helper('customer')->isLoggedIn()) {
            /** @var $update Mage_Core_Model_Layout_Update */
            $update = $observer->getEvent()->getLayout()->getUpdate();
            $groupId = Mage::helper('customer')->getCustomer()->getGroupId();
            $groupName = Mage::getModel('customer/group')->load($groupId)->getCode();
            $update->addHandle('customer_group_' . str_replace(' ', '_', strtolower($groupName)));
        }
     
        return $this;
    }
    
    /*
     * add new colums to customers grid
    */
    public function beforeBlockToHtml(Varien_Event_Observer $observer)
    {
        $grid = $observer->getBlock();

        /**
         * Mage_Adminhtml_Block_Customer_Grid
         */
        if ($grid instanceof Mage_Adminhtml_Block_Customer_Grid) {
            
            $grid->addColumnAfter(
                'business_descriptions_title',
                array(
                    'header'  => 'Denumire Comerciala',
                    'index'   => 'business_descriptions_title',
                    'type'    => 'text',
                ),
                'group'
            );
            
            $options = Mage::getModel('conturifurnizori/attribute_source_status')->getOptionArray();
            
            $grid->addColumnAfter(
                'furnizor_account_status',
                array(
                    'header'  => 'Account Status',
                    'index'   => 'furnizor_account_status',
                    'type'    => 'options',
                    'options' => $options
                ),
                'business_descriptions_title'
            );
            
            $options = Mage::getModel('conturifurnizori/attribute_source_acctype')->getOptionArray();
            
            $grid->addColumnAfter(
                'furnizor_account_type',
                array(
                    'header'  => 'Account Type',
                    'index'   => 'furnizor_account_type',
                    'type'    => 'options',
                    'options' => $options
                ),
                'furnizor_account_status'
            );
            
        }
    }

    /*
     * add newly added attributes to collection
    */
    public function beforeCollectionLoad(Varien_Event_Observer $observer)
    {
        $collection = $observer->getCollection();
        if (!isset($collection)) {
            return;
        }

        /**
         * Mage_Customer_Model_Resource_Customer_Collection
         */
        if ($collection instanceof Mage_Customer_Model_Resource_Customer_Collection) {
            /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
            $collection->addAttributeToSelect('business_descriptions_title');
            $collection->addAttributeToSelect('furnizor_account_status');
            $collection->addAttributeToSelect('furnizor_account_type');
        }
    }
    
    public function redirectAfterCouponApplied(Varien_Event_Observer $observer)
    {
        $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer() : Mage::getUrl();
        $parseUrl = parse_url($url);
        
        if ($parseUrl['path'] != '/conturifurnizori/inregistrare/index/step/review-info/' && $parseUrl['path'] != '/checkout/cart/') {
            $url .= 'index/step/review-info/';
        }
        
        Mage::log($observer->getCouponCode() . ' from ' . $url, false, 'register_provider.log');
        
        Mage::app()->getFrontController()->getResponse()->setRedirect($url);
        Mage::app()->getResponse()->sendResponse();
        
        return $this;
    }
       
    // public function hookIntoCatalogProductNewAction($observer)
    // {
        // $product = $observer->getEvent()->getProduct();
        // //echo 'Inside hookIntoCatalogProductNewAction observer...'; exit;
        // //Implement the "catalog_product_new_action" hook
        // return $this;        
    // }
//     
    // public function hookIntoCatalogProductEditAction($observer)
    // {
        // $product = $observer->getEvent()->getProduct();
        // //echo 'Inside hookIntoCatalogProductEditAction observer...'; exit;
        // //Implement the "catalog_product_edit_action" hook
        // return $this;        
    // }    
//     
    // public function hookIntoCatalogProductPrepareSave($observer)
    // {
        // $product = $observer->getEvent()->getProduct();
        // $event = $observer->getEvent();
        // //echo 'Inside hookIntoCatalogProductPrepareSave observer...'; exit;
        // //Implement the "catalog_product_prepare_save" hook
        // return $this;        
    // }
// 
    // public function hookIntoSalesOrderItemSaveAfter($observer)
    // {
        // //$event = $observer->getEvent();
        // //echo 'Inside hookIntoSalesOrderItemSaveAfter observer...'; exit;
        // //Implement the "sales_order_item_save_after" hook
        // return $this;        
    // }
// 
    // public function hookIntoSalesOrderSaveBefore($observer)
    // {
        // //$event = $observer->getEvent();
        // //echo 'Inside hookIntoSalesOrderSaveBefore observer...'; exit;
        // //Implement the "sales_order_save_before" hook
        // return $this;        
    // }     
//     
    // public function hookIntoSalesOrderSaveAfter($observer)
    // {
        // $product = $observer->getEvent()->getProduct();
        // //echo 'Inside hookIntoSalesOrderSaveAfter observer...'; exit;
        // //Implement the "sales_order_save_after" hook
        // return $this;        
    // } 
// 
    // public function hookIntoCatalogProductDeleteBefore($observer)
    // {
        // $product = $observer->getEvent()->getProduct();
        // //echo 'Inside hookIntoCatalogProductDeleteBefore observer...'; exit;
        // //Implement the "catalog_product_delete_before" hook
        // return $this;        
    // }    
//     
    // public function hookIntoCatalogruleBeforeApply($observer)
    // {
        // //$event = $observer->getEvent();
        // //echo 'Inside hookIntoCatalogruleBeforeApply observer...'; exit;
        // //Implement the "catalogrule_before_apply" hook
        // return $this;        
    // }  
// 
    // public function hookIntoCatalogruleAfterApply($observer)
    // {
        // //$event = $observer->getEvent();
        // //echo 'Inside hookIntoCatalogruleAfterApply observer...'; exit;
        // //Implement the "catalogrule_after_apply" hook
        // return $this;        
    // }    
//     
    // public function hookIntoCatalogProductSaveAfter($observer)
    // {
        // $product = $observer->getEvent()->getProduct();
        // $event = $observer->getEvent();
        // //echo 'Inside hookIntoCatalogProductSaveAfter observer...'; exit;
        // //Implement the "catalog_product_save_after" hook
        // return $this;        
    // }   
//  
    // public function hookIntoCatalogProductStatusUpdate($observer)
    // {
        // $product = $observer->getEvent()->getProduct();
        // $event = $observer->getEvent();
        // //echo 'Inside hookIntoCatalogProductStatusUpdate observer...'; exit;
        // //Implement the "catalog_product_status_update" hook
        // return $this;        
    // }
// 
    // public function hookIntoCatalogEntityAttributeSaveAfter($observer)
    // {
        // //$event = $observer->getEvent();
//         
        // //Implement the "catalog_entity_attribute_save_after" hook
        // return $this;        
    // }
//     
    // public function hookIntoCatalogProductDeleteAfterDone($observer)
    // {
        // $product = $observer->getEvent()->getProduct();
        // $event = $observer->getEvent();
        // //echo 'Inside hookIntoCatalogProductDeleteAfterDone observer...'; exit;
        // //Implement the "catalog_product_delete_after_done" hook
        // return $this;        
    // }
//     
    // public function hookIntoCustomerLogin($observer)
    // {
        // $event = $observer->getEvent();
        // //echo 'Inside hookIntoCustomerLogin observer...'; exit;
        // //Implement the "customer_login" hook
        // return $this;        
    // }       
// 
    // public function hookIntoCustomerLogout($observer)
    // {
        // $event = $observer->getEvent();
        // //echo 'Inside hookIntoCustomerLogout observer...'; exit;
        // //Implement the "customer_logout" hook
        // return $this;        
    // }
// 
    // public function hookIntoSalesQuoteSaveAfter($observer)
    // {
        // $event = $observer->getEvent();
        // //echo 'Inside hookIntoSalesQuoteSaveAfter observer...'; exit;
        // //Implement the "sales_quote_save_after" hook
        // return $this;        
    // }
// 
    // public function hookIntoCatalogProductCollectionLoadAfter($observer)
    // {
        // $event = $observer->getEvent();
        // //echo 'Inside hookIntoCatalogProductCollectionLoadAfter observer...'; exit;
        // //Implement the "catalog_product_collection_load_after" hook
        // return $this;        
    // }
    
}