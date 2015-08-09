<?php
class Picode_ConturiFurnizori_Block_Conturi_Providers extends Mage_Catalog_Block_Product_List
{
    protected $_productCollection;
    
    protected $_filterAttributes = array(
                            'furnizor_account_level',
                            'furnizor_company_type',
                            'furnizor_company_services',
                            'furnizor_company_zone',
                            'business_descriptions_exp',
                        );
    
    protected function _getProductCollection()
    {
        $params = $this->getRequest()->getParams();
        $action = Mage::app()->getRequest()->getActionName(); // could be: index, foto, video or foto-video
        
        if (is_null($this->_productCollection)) {
            
            $collection = Mage::getModel('customer/customer')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('furnizor_account_status', 1)
                ->addAttributeToFilter('furnizor_account_online_status', 1);
                
            // add region filter to collection
            if (isset($params['judet'])) {
                $collection->addAttributeToFilter('furnizor_location_province', $params['judet']);
                unset($params['judet'], $params['p']);
            }
            
            /*
             * aplly layer filters to collection
             * take a look into Picode_ConturiFurnizori_Block_Conturi_Layer
             */
            foreach ($params as $key => $val) {
                $attributeCode = $this->convertAttributeLabeToCode($key);
                $collection->addAttributeToFilter($attributeCode, $val);
            }
    
            // add service type filter to collection
            switch ($action) {
                case 'foto':
                    $collection->addAttributeToFilter('furnizor_company_services', 1);
                    break;
                case 'video':
                    $collection->addAttributeToFilter('furnizor_company_services', 2);
                    break;
                case 'fotovideo';
                    $collection->addAttributeToFilter('furnizor_company_services', 3);
                    break;
            }
            
            //Zend_Debug::dump($collection->getData()); die();
            //$collection->setOrder('business_images_logo', 'DESC');
            $this->_productCollection = $collection;
        }

        return parent::_getProductCollection();
    }

    public function convertAttributeLabeToCode($attributeLable)
    {
        foreach ($this->_filterAttributes as $attribute) {
            
            $details = Mage::getSingleton('eav/config')->getAttribute('customer', $attribute);
            if (strtolower(str_replace(' ', '-', $details->getFrontendLabel())) == $attributeLable) {
                return $details->getAttributeCode();
            }
        }
        
        return;
    }

    public function truncate($text, $length)
    {
       $length = abs((int)$length);

       if(strlen($text) > $length) {
          $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
       }
       return($text);
    }

    /*** view page ***/

    public function getLoadedProviderDetails()
    {
        $params = $this->getRequest()->getParams();

        foreach ($params as $key => $val) {
            if (is_numeric($val)) {
                $providerId = (int) $val;
            } else {
                $providerId = false;
                unset($key);
            }
        }

        if ($providerId) {
            $provider = Mage::getModel('customer/customer')->load($providerId);
        } else {
            return;
        }

        return $provider;
    }

    public function getProviderOffers($providerId)
    {
        $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('url_path')
                ->addAttributeToSelect('ofg_tip_oferta')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('oferta_speciala')
                ->addAttributeToSelect('special_price')
                ->addAttributeToFilter('ofg_customer_id', $providerId)
                ->addAttributeToFilter('status', 1)
                ->addAttributeToFilter('visibility', 4);

        return $collection;
    }

    public function getProviderAttributesBySet($_provider, $attrPrefix = false)
    {
        $i = 0;
        $attributes = array();

        foreach ($_provider->getData() as $key => $value) {
            //echo $key . '<br />';
            if (strpos($key, $attrPrefix) !== false && $value != '') {
                $attribute = $_provider->getResource()->getAttribute($key);
                if ($attribute->getFrontendInput() == 'textarea') {
                    // skip descriptions
                    continue;
                } else {
                    $attributes[$i]['title'] = $_provider->getResource()->getAttribute($key)->getStoreLabel();;
                    $attributes[$i]['value'] = $value;
                    $i++;
                }

            }
        }

        return $attributes;
    }
}
