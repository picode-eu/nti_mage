<?php

class Picode_ConturiFurnizori_Block_Conturi_Furnizori extends Mage_Core_Block_Template
{
    protected $_defaultAvailableLimit  = array(10 => 10, 24 => 24, 40 => 40, 56 => 56);

    public function __construct()
    {
        $params = $this->getRequest()->getParams();
        $action = Mage::app()->getRequest()->getActionName(); // could be: index, foto, video or foto-video
        //Zend_Debug::dump($params); die();
        parent::__construct();

        $collection = Mage::getModel('customer/customer')
                ->getCollection()
                
                ->addAttributeToSelect('furnizor_account_type')
                ->addAttributeToSelect('furnizor_company_name')
                ->addAttributeToSelect('furnizor_company_type')

                ->addAttributeToSelect('furnizor_company_services')
                ->addAttributeToSelect('furnizor_company_cstzone')
                ->addAttributeToSelect('furnizor_location_province')
                ->addAttributeToSelect('furnizor_location_city')

                ->addAttributeToSelect('business_images_logo')
                ->addAttributeToSelect('business_descriptions_slogan')
                ->addAttributeToSelect('business_descriptions_exp')
                ->addAttributeToSelect('business_descriptions_slogan')
                ->addAttributeToSelect('business_descriptions_desc')
                ->addAttributeToSelect('furnizor_company_zone')
                ->addAttributeToSelect('provider_reputation')
                ->addAttributeToSelect('provider_views')

                ->addAttributeToSelect('business_descriptions_title')
                ->addAttributeToFilter('group_id', 4)
                ->addAttributeToFilter('furnizor_account_status', array('in' => array(1,2))) // 1 is for Active and 2 is for "Pending Payment"
                ->addAttributeToFilter('furnizor_account_online_status', 1)
                ->addAttributeToFilter('ac_op_afisare_profil', 1)
                ;
                
        //$collection->getSelect()->order('e.provider_reputation DESC');

        // add region filter to collection
        if (isset($params['judet'])) {
            $collection->addAttributeToFilter('furnizor_location_province', $params['judet']);
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

        // Zend_Debug::dump($collection->getSize());
        $collection->setOrder('provider_reputation', 'DESC');
                
        $this->setCollection($collection);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');

        $pager->setAvailableLimit($this->_defaultAvailableLimit);
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();

        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
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
            
            //if ($provider->getFurnizorAccountOnlineStatus() != 0) {
                return $provider;
            //}
        }
        
        Mage::app()->getFrontController()->getResponse()->setRedirect($this->getBaseUrl() . 'conturifurnizori/furnizori/');
        return false;
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

