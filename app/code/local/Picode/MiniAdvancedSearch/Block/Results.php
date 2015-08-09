<?php
class Picode_MiniAdvancedSearch_Block_Results extends Picode_ConturiFurnizori_Block_Conturi_Providers
{
    protected $_productCollection;
    protected $_fullSize;
    protected $_limit = 10;

    public function getQuickSearchResult()
    {
        $params = $this->getRequest()->getParams();
        $sessionParams = Mage::getSingleton('core/session')->getParams();
        $regiterParams = Mage::registry('params');

        foreach ($sessionParams as $key => $param) {
            if (!isset($params[$key])) $params[$key] = $param;
        }

//        foreach ($regiterParams as $key => $param) {
//            $params[$key] = $param;
//        }

        if ($params['search_for'] == 'furnizori') {
            $collection = $this->_getProviderQuickCollection();
        } elseif ($params['search_for'] == 'oferte') {
            $collection = $this->_getOfferQuickCollection();
        } else {
            return;
        }

        return $this->_productCollection = $collection;
    }

    private function _getProviderQuickCollection()
    {
        $params = $this->getRequest()->getParams();

        $collection = Mage::getModel('customer/customer')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('furnizor_account_status', 1)
            ->addAttributeToFilter('furnizor_account_online_status', 1);

        if (isset($params['region']) && $params['region'] != '') {
            $collection->addAttributeToFilter('furnizor_location_province', $params['region']);
        }

        if (isset($params['services']) && $params['services'] != '') {
            $collection->addAttributeToFilter('furnizor_company_services', $params['services']);
        }

        if (isset($params['experienta']) && $params['experienta'] != '') {
            $collection->addAttributeToFilter('business_descriptions_exp', $params['experienta']);
        }

//        if (isset($params['prices']) && $params['prices'] != '') {
//            $priceRange = explode('-', $params['prices']);
//            $collection
//                ->addAttributeToFilter('price', array('gt' => min($priceRange)))
//                ->addAttributeToFilter('price', array('lt' => max($priceRange)));
//        }

        if (isset($params['denumire']) && $params['denumire'] != '') {
            $collection->addAttributeToFilter('business_descriptions_title', array('like' => '%' . $params['denumire'] . '%'));
        }

        if ($collection->getSize() > $this->_limit) {
            $this->setFullSize($collection->getSize());
            $collection->setPageSize($this->_limit)->setCurPage(1);
        }

        $collection->setOrder('provider_reputation', 'desc');

        foreach ($collection as $_item) {
            $_item->setData('item_entity_type', 'provider');
            $_item->setData('item_name', $_item->getBusinessDescriptionsTitle());
            //$_item->setData('item_price', 'Pret ' . Mage::helper('core')->currency($_item->getFinalPrice(), true, false));
            $_item->setData('item_services', 'Servicii ' . strtoupper($_item->getResource()->getAttribute('furnizor_company_services')->getFrontend()->getValue($_item)));
            $_item->setData('item_rpf', $_item->getProviderReputation());
            $_item->setData('item_sediu', 'Sediul ' . $_item->getResource()->getAttribute('furnizor_location_province')->getFrontend()->getValue($_item));
            $_helper = Mage::helper('conturifurnizori');
            $providerUrl = $this->getBaseUrl() . 'conturifurnizori/furnizori/profil/' . $_helper->seoFriendlyUrl($_item->getBusinessDescriptionsTitle()) . '/' . $_item->getId();
            $_item->setData('item_url', $providerUrl);
            $_item->setData('item_image', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'customer' . $_item->getBusinessImagesLogo());
        }

        return $collection;
    }

    private function _getOfferQuickCollection()
    {
        $params = $this->getRequest()->getParams();

        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', 4)
            ->addAttributeToFilter('attribute_set_id', 25);

        if (isset($params['region']) && $params['region'] != '') {
            $collection->addAttributeToFilter('ofg_zona_personalizata', $params['region']);
        }

        if (isset($params['services']) && $params['services'] != '') {
            $collection->addAttributeToFilter('ofg_tip_oferta', $params['services']);
        }

        if (isset($params['prices']) && $params['prices'] != '') {
            $priceRange = explode('-', $params['prices']);
            $collection
                ->addAttributeToFilter('price', array('gt' => min($priceRange)))
                ->addAttributeToFilter('price', array('lt' => max($priceRange)));
        }

        if (isset($params['denumire']) && $params['denumire'] != '') {
            $collection->addAttributeToFilter('name', array('like' => '%' . $params['denumire'] . '%'));
        }

        $corelation = 'provider_reputation_' . uniqid();
        $corelation_p = 'product_varchar_' . uniqid();

        $table = Mage::getSingleton('core/resource')->getTableName('reputationpoints/reputation');
        $table_p = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_varchar');

        $collection->getSelect()
            ->joinLeft(
                array($corelation_p => $table_p),
                "e.entity_id = $corelation_p.entity_id",
                array("ofg_customer_id" => "$corelation_p.value")
            )
            ->where("$corelation_p.attribute_id = ?", "309")

            ->joinLeft(
                array($corelation => $table),
                "$corelation_p.value = $corelation.provider_id",
                array("earned_points" => "$corelation.earned_points")
            )
            ->where("$corelation.earned_points is not null")
            ->order("earned_points desc")
        ;

        if ($collection->getSize() > $this->_limit) {
            $this->setFullSize($collection->getSize());
            $collection->setPageSize($this->_limit)->setCurPage(1);
        }

        foreach ($collection as $_item) {
            $_item->setData('item_entity_type', 'offer');
            $_item->setData('item_name', $_item->getName());
            $_item->setData('item_price', 'Pret ' . Mage::helper('core')->currency($_item->getFinalPrice(), true, false));
            $_item->setData('item_services', 'Oferta ' . strtoupper($_item->getAttributeText('ofg_tip_oferta')));
            $_item->setData('item_rpf', $_item->getEarnedPoints());
            $_item->setData('item_url', $_item->getUrlPath());
            $_item->setData('item_image', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product/' . $_item->getImage());
        }

        return $collection;
    }

    public function setFullSize($size)
    {
        $this->_fullSize = $size;
    }

    public function getFullSize()
    {
        return $this->_fullSize;
    }
}