<?php
class Picode_ConturiFurnizori_Block_Oferte_Left extends Mage_Catalog_Block_Product_List
{
    protected $_customer;
        
    public function __construct()
    {
        $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
    }
    
    public function getActiveProducts()
    {
        $collection = Mage::getModel('catalog/product')
                        ->getCollection()
                        ->addAttributeToSelect('name')
                        ->addAttributeToSelect('visibility')
                        ->addAttributeToFilter('ofg_customer_id', $this->_customer->getId())
                        ->addAttributeToFilter('visibility', 4);
                        
        return $collection;
    }
    
    public function getInactiveProducts()
    {
        $collection = Mage::getModel('catalog/product')
                        ->getCollection()
                        ->addAttributeToSelect('name')
                        ->addAttributeToSelect('visibility')
                        ->addAttributeToFilter('ofg_customer_id', $this->_customer->getId())
                        ->addAttributeToFilter('visibility', 1);
                        
        return $collection;
    }
    
    public function getEditUrl($_product = false)
    {
        if ($_product) {
            // edit an existing product
            return Mage::getUrl('conturifurnizori/oferte/editeaza/', array('_secure' => true, 'oferta' => $_product->getId()));
        } else {
            // add new product
            return Mage::getUrl('conturifurnizori/oferte/editeaza/', array('_secure' => true, 'oferta' => 'noua'));
        }
        
    }
    
    public function getIsCurrentLink($_product)
    {
        $param = $this->getRequest()->getParam('oferta');
        if (isset($param) && $param == $_product->getId()) {
            return true;
        } else {
            return false;
        }
    }
}