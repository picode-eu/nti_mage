<?php
class Picode_Overwrite_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_List
{
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        $judet = Mage::app()->getRequest()->getParam('judet');
        
        if ($judet) {

            $collection = parent::_getProductCollection()->clear();

            foreach ($collection as $key => $product) {
                if ($judet && strpos($product->getOfgZonaPersonalizata(), $judet) === false) {
                    $collection->removeItemByKey($key);
                }
            }

            $this->_productCollection = $collection;
            return $this->_productCollection;

        }

        return parent::_getProductCollection();
    }
    
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    // public function getLoadedProductCollection()
    // {
        // $judet = Mage::app()->getRequest()->getParam('judet');
//
        // if ($judet) {
            // $collection = $this->_getProductCollection();
//
            // foreach ($collection as $key => $product) {
                // if (strpos($product->getOfgZonaPersonalizata(), $judet) === false) {
                    // $collection->removeItemByKey($key);
                // }
            // }
//
            // return $collection;
        // }
//
        // return $this->_getProductCollection();
    // }
    
    public function truncate($text, $length)
    {
       $length = abs((int)$length);
       if(strlen($text) > $length) {
          $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
       }
       return($text);
    }
    
    public function getProviderInfo($providerId, $attrCode)
    {
        $_customer = Mage::getModel('customer/customer')->load($providerId);
        return $_customer->getResource()->getAttribute($attrCode)->getFrontend()->getValue($_customer);
        
        //return Mage::getModel('customer/customer')->load($providerId);
    }
}
