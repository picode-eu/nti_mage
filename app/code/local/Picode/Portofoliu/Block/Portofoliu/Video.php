<?php   
class Picode_Portofoliu_Block_Portofoliu_Video extends Mage_Core_Block_Template
{
	private $_customerSession = null;
	private $_customerId = null;
	private $_redirectUrl;
	private $_params;
	
	public function __construct()
	{
		$this->_coreSession = Mage::getSingleton('core/session');
        $this->_customerSession = Mage::getSingleton('customer/session');
        $this->_customerId = $this->_customerSession->getCustomer()->getId();
        $this->_params = $this->getRequest()->getParams();
        $this->_controller = $this->getRequest()->getControllerName();
        $this->_action = $this->getRequest()->getActionName();
	}
	
	private function _redirect()
	{
		return Mage::app()->getFrontController()->getResponse()->setRedirect($this->_redirectUrl);
	}
	
	public function requestedCollection()
    {    
        $portofoliuModel = Mage::getModel('portofoliu/videos');
        
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
                    $this->_coreSession->addNotice('Videoclipul nu a fost găsit. Acesta ori a fost șters ori a devenit privat.');
                    $url = Mage::getBaseUrl() . 'portofoliu/video/';
                    Mage::app()->getFrontController()->getResponse()->setRedirect($url);
                }
                
                /*
                 * update album view and unic visit count
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
                        // $timeLimit = 60; // 1 minute (for testing)

                        if ($diff > $timeLimit) {
                            $newVisit = $visitModel
                                ->setPortofoliuType('video')
                                ->setPortofoliuId($collection->getVideoId())
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
                            ->setPortofoliuType('video')
                            ->setPortofoliuId($collection->getVideoId())
                            ->setCustomerIdentifier($customerIdentifier)
                            ->setCustomerId($customerId);
                        $newVisit->save();

                        // update viewed details
                        $videoViewed = $collection->getVisitCount() + 1;
                        $collection
                            ->setVisitCount($videoViewed)
                            ->save();
                    }
                }

                break;
                
            default:
                $collection = $portofoliuModel->getCollection()
                                    ->addFieldToFilter('is_visible', 1);
                
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
                    ->setOrder('visit_count','desc')
                    ->setOrder('created_at', 'asc');
                
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
    
    public function getEmbedCode($urlType, $videoDbId)
    {
        if($urlType == 'youtube'){
            // return '<iframe width="525" height="263" src="//www.youtube.com/embed/'.$videoDbId.'" frameborder="0" allowfullscreen></iframe>';
            return '<iframe src="//www.youtube.com/embed/'.$videoDbId.'" frameborder="0" allowfullscreen></iframe>';
        }elseif($urlType == 'vimeo'){
            return '<iframe src="//player.vimeo.com/video/'.$videoDbId.'" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        }
    }

    public function getVimeoThumb($_video, $size = false)
    {
        $thumbUrl = '';

        if ($_video->getUrlType() == 'youtube') {
            $thumbUrl = 'http://img.youtube.com/vi/' . $_video->getVideoUrl() . '/hqdefault.jpg';
        } elseif ($_video->getUrlType() == 'vimeo') {
            $url = 'vimeo.com/api/v2/video/' . $_video->getVideoUrl() . '.php';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $data = curl_exec($ch);

            if (curl_error($ch)) {
                return false;
            }

            $finalData = unserialize($data);
            $thumbUrl = $finalData[0]['thumbnail_medium'];

        }

        return $thumbUrl;

    }


}