<?php 

/**
 * Adminhtml provider action tab
 *
 */
class Picode_ConturiFurnizori_Block_Adminhtml_Furnizori_Edit_Tab_Details 
    extends Mage_Adminhtml_Block_Template 
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        $this->setTemplate('picode/conturifurnizori/details.phtml');

    }

    public function getCustomtabInfo(){

        $customer = Mage::registry('current_customer');
        //Zend_Debug::dump($customer->getData());
        $attributes = Mage::getModel('customer/customer')->getAttributes();
        
        foreach ($attributes as $attr) {
            //Zend_Debug::dump(get_class_methods($attr));
            $attrCodeArr = explode('_', $attr->getAttributeCode());
            
            if (in_array('furnizor', $attrCodeArr)) {
                //Zend_Debug::dump($attr->getAttributeCode());
                $providerDetails[] = $attr->loadByCode('customer', $attr->getAttributeCode());
            }
        }
        
        if (isset($providerDetails)) return $providerDetails;
        else return false;
    }
    
    public function getCustomer()
    {
        return Mage::registry('current_customer');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Provider Information');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Provider Information');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        return (bool)$customer->getId();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

     /**
     * Defines after which tab, this tab should be rendered
     *
     * @return string
     */
    public function getAfter()
    {
        return 'account';
    }
    
    public function getAttributeOptions($attribute)
    {
        return Mage::getModel($attribute->getSourceModel())->getAllOptions();
    }
    
    public function getFurnizorSegment($info)
    {
        $infoArr = explode('_', $info->getAttributeCode());
        return ucfirst($infoArr[1]);
    }
    
}
?>