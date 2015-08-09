<?php
class Picode_Overwrite_Model_Catalog_Config extends Mage_Catalog_Model_Config
{
	public function getAttributeUsedForSortByArray()
	{
	    $controller = Mage::app()->getRequest()->getControllerName();
        
        if ($controller == 'category') {
            $options = array(
                'reputatie-furnizor' => Mage::helper('catalog')->__('Reputatie Furnizor (RPF)'),
                'popularitate' => Mage::helper('catalog')->__('Popularitate Oferta (PPO)'),
                // 'review' => Mage::helper('catalog')->__('Comentarii'),
                // 'loves' => Mage::helper('catalog')->__('Aprecieri'),
                // 'provider' => Mage::helper('catalog')->__('Tip furnizor'),
               );
               
            // add the rest of the sort order to array
            foreach ($this->getAttributesUsedForSortBy() as $attribute)
            {
                /* @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */
                $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
            }
        } elseif ($controller == 'furnizori') {
            $options = array(
                    'popularitate_furnizor' => Mage::helper('catalog')->__('Reputație Furnizor (RPF)'),
                    'nume' => Mage::helper('catalog')->__('Denumire Furnizor'),
                    'afisari' => Mage::helper('catalog')->__('Afișări'),
                    // 'experienta' => Mage::helper('catalog')->__('Experienta'),
                    //'servicii' => Mage::helper('catalog')->__('Servicii'),
                   );
        } else {
            $options = array(
                    'nume' => Mage::helper('catalog')->__('Nume'),
                    );
        }

		return $options;
	}
    
    public function getProductListDefaultSortBy($store = null)
    {
        $controller = Mage::app()->getRequest()->getControllerName();
        
        if ($controller == 'category'){
            // return 'popularitate';
            return 'reputatie-furnizor';
        } elseif ($controller == 'furnizori') {
            return 'popularitate_furnizor';
        }
        
    }
    
}
