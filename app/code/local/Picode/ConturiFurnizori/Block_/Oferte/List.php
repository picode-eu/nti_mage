<?php
class Picode_ConturiFurnizori_Block_Oferte_List extends Mage_Catalog_Block_Product_List
{
    protected $_typeOferta;
    protected $_customer;

    public function __construct()
    {
        $this->_typeOferta = $this->getRequest()->getParam('type');
        $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getOfertaCollection()
    {
        $productCollection = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('ofg_customer_id', $this->_customer->getId());

        switch ($this->_typeOferta) {
            case 'active':
                $productCollection->addAttributeToFilter('status', 1); // 1 is for enabled
                break;

            case 'inactive':
                $productCollection->addAttributeToFilter('status', 2); // 2 is disabled
                break;
        }

        return $productCollection;
    }

    public function getLastAddedOffers($back, $limit = false)
    {
        $offersCollection = Mage::getModel('catalog/product')
                ->getCollection()
                //->addAttributeToSelect('*')
                ->addAttributeToSelect(array(
                    'name', 'ofg_tip_oferta', 'price', 'special_price', 'special_from_date', 'special_to_date'
                ))
                ->addAttributeToFilter('ofg_customer_id', array('notnull' => true))
                ->addAttributeToFilter('type_id', 'ofertefurnizori')
                ->addAttributeToFilter('status', 1)
                ->addAttributeToFilter('visibility', 4)
                ->addAttributeToFilter('image', array('neq' => 'no_selection'))
                ;

        if ($limit) {
            $offersCollection->setPageSize($limit)->setCurPage(1);
        }

        if ($back) {
            $endDate = date('Y-m-d 00:00:00', Mage::getModel('core/date')->timestamp(time()));
            $startDate = date('Y-m-d 00:00:00', strtotime($endDate . ' - ' . $back . ' day'));

            $offersCollection->addFieldToFilter('created_at', array(array('from' => $startDate, 'to' => $endDate)));
        }

        $offersCollection->setOrder('created_at', 'DESC');

        return $offersCollection;
    }

    public function getProviderFromOffer($providerId)
    {
        return Mage::getModel('customer/customer')->load($providerId);
        //Zend_Debug::dump($_provider->getData()); die();
    }
}