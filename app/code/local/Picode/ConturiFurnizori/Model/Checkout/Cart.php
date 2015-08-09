<?php
class Picode_ConturiFurnizori_Model_Checkout_Cart extends Mage_Checkout_Model_Cart
{
    /**
     * Create checkout_cart_product_add_before event observer
     *
     * @param   int|Mage_Catalog_Model_Product $productInfo
     * @param   mixed $requestInfo
     * @return  Mage_Checkout_Model_Cart
     */
    public function addProduct($productInfo, $requestInfo = null)
    {
//        Zend_Debug::dump($productInfo->getData());
//        Zend_Debug::dump($requestInfo);
//        die('cart');

        $product = $this->_getProduct($productInfo);
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		
        Mage::dispatchEvent('checkout_cart_product_add_before', array('product' => $product, 'quote' => $quote));
         
        return parent::addProduct($productInfo, $requestInfo);
    }
}