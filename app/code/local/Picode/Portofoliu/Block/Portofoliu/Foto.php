<?php
class Picode_Portofoliu_Block_Portofoliu_Foto extends Mage_Core_Block_Template
{
	private $_coreSession = null;
	private $_customerSession = null;
	private $_customerId = null;
	private $_params;
	private $_controller;
	private $_action;

	public function __construct()
	{
		$this->_coreSession = Mage::getSingleton('core/session');
		$this->_customerSession = Mage::getSingleton('customer/session');
		$this->_customerId = $this->_customerSession->getCustomer()->getId();
		$this->_params = $this->getRequest()->getParams();
		$this->_controller = $this->getRequest()->getControllerName();
		$this->_action = $this->getRequest()->getActionName();
	}

	private function _redirect($url)
	{
		return Mage::app()->getFrontController()->getResponse()->setRedirect($this->getBaseUrl() . $url);
	}

	public function requestedCollection()
	{
		$portofoliuModel = Mage::getModel('portofoliu/albums');

		switch($this->_action){
			case 'view':
				$params = $this->getRequest()->getParams();
				$i = 0;
				foreach ($params as $key => $val) {
					if ($i == 0) {
						$params['provider_id'] = (int)$val;
						unset($params[$key]);
					}
					if ($i == 1) {
						$params['id'] = $val;
						unset($params[$key]);
					}
					$i++;
				}

				$isVisible = false;
                $collection = $portofoliuModel->load($params['id']);
                if ($collection) {
                    if ($collection->getId() && $collection->getIsVisible()) {
                        $_customer = Mage::getModel('customer/customer')->load($collection->getCustomerId());
                        if ($_customer->getFurnizorAccountStatus() == 1) {
                            $isVisible = true;
                        }
                    }
                }

                if(!$isVisible){
					$this->_coreSession->addNotice('Albumul foto nu a fost găsit. Acesta ori a fost șters ori a devenit privat.');
					$url = Mage::getBaseUrl() . 'portofoliu/foto/';
					Mage::app()->getFrontController()->getResponse()->setRedirect($url);
				}

				/*
				 * update album viewed and unic visit count
				 * if customer is not the album owner
				*/
				if($this->_customerId != $collection->getCustomerId()){

					$customerIdentifier = $this->helper('reputationpoints')->getCustomerIdentifier();
					$customerId = $this->_customerId ? $this->_customerId : 0;
					$visitModel = Mage::getModel('portofoliu/visitors');

					// register new visit
					$lastVisit = $visitModel->getCollection()
						->addFieldToFilter('customer_identifier', $customerIdentifier)
						->addFieldToFilter('portofoliu_id', $collection->getId())
						->setOrder('created_at', 'DESC')
						->getFirstItem();

					if ($lastVisit->getId()) {
						$lastVisit->getCreatedAt();
						$lastCreated = strtotime($lastVisit->getCreatedAt());
						$now = strtotime(date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time())));
						$diff = $now - $lastCreated;
						$timeLimit = 43200; // 12 hours
						//$timeLimit = 30; // 1 minute (for testing)

						if ($diff > $timeLimit) {
							$newVisit = $visitModel
								->setPortofoliuType('foto')
								->setPortofoliuId($collection->getAlbumId())
								->setCustomerIdentifier($customerIdentifier)
								->setCustomerId($customerId);
							$newVisit->save();

							// update viewed details
							$albumViewed = $collection->getVisitCount() + 1;
							$collection
								->setVisitCount($albumViewed)
								->save();
						}
					} else {
						$newVisit = $visitModel
							->setPortofoliuType('foto')
							->setPortofoliuId($collection->getAlbumId())
							->setCustomerIdentifier($customerIdentifier)
							->setCustomerId($customerId);
						$newVisit->save();

						// update viewed details
						$albumViewed = $collection->getVisitCount() + 1;
						$collection
							->setVisitCount($albumViewed)
							->save();
					}
				}

				break;

			default:
				$collection = $portofoliuModel->getCollection()
                					->addFieldToFilter('is_visible', 1)
                                    ->addFieldToFilter('album_cover', array('notnull' => true));

                                    //Zend_Debug::dump($collection->getData());

                $collection->getSelect()
                                ->joinLeft(
                                    array('cstint' => Mage::getSingleton('core/resource')->getTableName('customer_entity_int')),
                                    'main_table.customer_id=cstint.entity_id and cstint.attribute_id = 364', // 364 is for furnizor_account_status
                                    array('provider_status' => 'value')
                                )
                                ->where('cstint.value=?', '1') // 1 is for Active

                                ->joinLeft(
                                    array('cstvrc1' => Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar')),
                                    'main_table.customer_id=cstvrc1.entity_id and cstvrc1.attribute_id = 258', // 258 is for furnizor_account_online_status
                                    array('provider_visibility' => 'value')
                                )
                                ->where('cstvrc1.value=?', '1') // 1 is for Public

                               ->joinLeft(
                                    array('cstint1' => Mage::getSingleton('core/resource')->getTableName('customer_entity_int')),
                                    'main_table.customer_id=cstint1.entity_id and cstint1.attribute_id = 367', // 364 is for provider_reputation
                                    array('provider_reputation' => 'value')
                                )
                                // ->order('provider_reputation desc')
                                // ->order('views desc')
                                // ->order('created_at asc')
                                ;


                $collection
                    ->setOrder('provider_reputation','desc')
                    ->setOrder('views','desc');

				break;

		}
		// set pager block
		$pager = $this->getLayout()->createBlock('page/html_pager', 'pager');
        $pager->setAvailableLimit(array(24 => 24, 36 => 36, 60 => 60));
		$pager->setCollection($collection);
        $this->setChild('pager', $pager);
		// return collection with pager block
		return $collection;
	}

	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

	public function geProviderById($customerId)
	{
		return Mage::getModel('customer/customer')->load($customerId);
	}

	public function getPhotosByAlbumId($albumId)
	{
		$collection = Mage::getModel('portofoliu/photos')
			->getCollection()
			->addFieldToFilter('album_id', $albumId)
			->setOrder('sort_order', 'ASC');

        //Zend_Debug::dump($collection->getData());

		return $collection;
	}

	public function furnizorIsActive($furnizorId)
	{
		$product = Mage::getModel('catalog/product')->loadByAttribute('id_furnizor', $furnizorId);

		if($product):
			if($product->getStatus() == 1) return true;
			else return false;
		else:
			return false;
		endif;
	}

	public function shortenString($string, $width, $more = false)
	{
		if(strlen($string) > $width) {
	    	$string = wordwrap($string, $width);
	    	$string = substr($string, 0, strpos($string, "\n"));
			if($more) $string .= ' ...';
	  	}

	  	return $string;
	}
}
