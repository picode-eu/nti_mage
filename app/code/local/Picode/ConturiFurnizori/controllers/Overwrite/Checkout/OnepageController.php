<?php

require_once Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'OnepageController.php';
//we need to add this one since Magento wont recognize it automatically

class Picode_ConturiFurnizori_Overwrite_Checkout_OnepageController extends Mage_Checkout_OnepageController
{
    /**
     * Checkout page
     */
    public function indexAction()
    {
    	$quote = $this->getOnepage()->getQuote();
		$items = $quote->getItemsCollection();
		
		foreach ($items as $item) {
			if ($item->getProductType() == 'conturifurnizori') {
				$this->_redirect('conturifurnizori/inregistrare/');
				return;
			}
		}
        
		return parent::indexAction();
    }
}
