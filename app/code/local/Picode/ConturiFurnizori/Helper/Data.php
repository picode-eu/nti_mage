<?php

class Picode_ConturiFurnizori_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getAttributesFromGroup($groupId)
    {
        $attributesCollection = Mage::getResourceModel('catalog/product_attribute_collection')
                    ->setAttributeGroupFilter($groupId);

        return $attributesCollection;
    }

    public function getFormatedAttributeValue($attributeCode, $_product)
    {
        switch ($attributeCode) {
            // Yes/No type attributes
            case 'cont_afisare_preferentiala':
            case 'cont_afisare_profil':
            case 'cont_afisare_oferte':
            case 'cont_eticheta_oferta_speciala':
            case 'cont_afisare_album_prezentare':
            case 'cont_afisare_video_prezentare':
            case 'cont_link_restul_ofertelor':
            case 'cont_link_alte_oferte':
            case 'cont_notificari_email':
            case 'cont_notificari_sms':
            case 'cont_link_direct_website':
            case 'cont_afisare_retele':
            case 'cont_rapoarte_avansate':
                // $_product->getData($attributeCode) ? $formatedAttribute = '&#46;' :  $formatedAttribute = '&#215;';
                $_product->getData($attributeCode) ? $formatedAttribute = '<span class="symbol green">&#46;</span>' :  $formatedAttribute = '<span class="symbol red">&#39;</span>';
                break;
            // integer type attributes
            case 'cont_max_oferte_active':
            case 'cont_max_album_active':
            case 'cont_max_video_active':
                $attributeValue = $_product->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($_product);
                $formatedAttribute = $attributeValue ? 'Maxim ' . $attributeValue : '<span class="symbol red">&#39;</span>';
                break;
            case 'cont_spatiu_disc':
                $attributeValue = $_product->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($_product);
                $formatedAttribute = $attributeValue ? $attributeValue . 'MB' : '<span class="symbol red">&#39;</span>';
                break;
            default:
                $formatedAttribute = $_product->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($_product);
                break;
        }

        return $formatedAttribute;
    }

    public function convertFileSize($bytes, $decimals = 2)
    {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    public function convertReputationPoints($points, $decimals = 0)
    {
        $decimals = $points > 999 ? 2 : 0;
        $size = array('','k','M','G','T','P','E','Z','Y');
        $factor = floor((strlen($points) - 1) / 3);

        return sprintf("%.{$decimals}f", $points / pow(1000, $factor)) . @$size[$factor];
    }

    /**
     * Check account type
     *
     * @return bool
     */
    public function accountIsPaid()
    {
        $itemsCollection = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
        // iterate through items collectio
        foreach ($itemsCollection as $item) {
            // check if account type from cart is a paid one
            if ($item->getProduct()->getContTip() == 'platit') {
                return true;
            } else {
                return false;
            }
        }
    }

    public function setAccountStatus($status)
    {
        $customer = $this->getCustomer();
        $customer->setFurnizorAccountStatus($status);
        $customer->save();

        return;
    }

	/**
     * Get customer group code (name)
     *
     * @return string or false
     */
    public function getCustomerGroupCode($groupId = false)
	{
		// get the logged in customer group id in case of no id was supplied
		if (!$groupId) $groupId = $this->getCustomer()->getGroupId();

		if ($groupId) {
			return strtolower(Mage::getModel('customer/group')->load($groupId)->getCode());
		}

		return false;
	}

	/**
     * Translate month name from en to ro
     *
     * @return string
     */
    public function translateDate($string)
	{
		$stringArr = explode(' ', $string);

        if (!is_array($stringArr)) {
            $stringArr = explode('-', $string);
        }

		switch (strtolower($stringArr[1])) {
			case 'jan':
				$translate = 'ian.';
				break;
			case 'feb':
				$translate = 'feb.';
				break;
			case 'mar':
				$translate = 'mar.';
				break;
			case 'apr':
				$translate = 'apr.';
				break;
			case 'may':
				$translate = 'mai';
				break;
			case 'jun':
				$translate = 'iun.';
				break;
			case 'jul':
				$translate = 'iul.';
				break;
			case 'aug':
				$translate = 'aug.';
				break;
			case 'sep':
				$translate = 'sept.';
				break;
			case 'oct':
				$translate = 'oct.';
				break;
			case 'nov':
				$translate = 'nov.';
				break;
			case 'dec':
				$translate = 'dec.';
				break;
			default:
				$translate = $stringArr[1];
				break;
		}

		if ($translate == $stringArr[1]) {
			return $string;
		} else {
			return $stringArr[0] . ' ' . $translate . ' ' . $stringArr[2];
		}
	}

    public function getAccountTypeNameBylLevel($attrCode, $accountLevel)
    {
        $productCollection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect($attrCode)
            ->addAttributeToFilter('attribute_set_id', 9);
            //->addAttributeToFilter($attrCode, $accountLevel);
            //->getFirstItem();

        foreach ($productCollection as $_product) {
            $_product = Mage::getModel('catalog/product')->load($_product->getId());

            if ($_product->getContLevel() == $accountLevel) {
                return $_product;
            }
        }

        return false;
    }

    /*
     * return the last order placed by a customer
     */
    public function getCustomerLastOrder($customerId = false)
    {
        if (!$customerId) {
            $customerId = $this->getCustomer()->getId();
        }

        $orders = Mage::getModel('sales/order')->getCollection()
                            ->addAttributeToFilter('customer_id', $customerId)
                            ->setOrder('created_at', 'desc')
                            ->getFirstItem();

        if ($orders) {
            return $orders;
        }

        return false;
    }

    /**
	 * load or create a new object that will store offer form data
	 */
	public function getOfertaObj()
    {
        $ofertaObj = new Varien_Object();

        $serializedOferta = Mage::getSingleton('customer/session')->getOfertaObject();
        if ($serializedOferta) {
            $ofertaObj = unserialize($serializedOferta);
            //die('there is an offer');
        }

        //Zend_Debug::dump($ofertaObj); die('helper');
        return $ofertaObj;
    }

    /**
     * generate an unic key usefull for skus, ids, etc...
     */
    public function generateUnicKey($string = false)
    {
         $unicIdArr = explode('.',uniqid('', true));
         $unicId = substr($unicIdArr[1],-6).rand(10,99);

         if($string){ /* genereaza url */
              return strtolower(str_replace(' ','-',strip_tags($string))).'-'.$unicId;
         }else{ /* genereaza sku */
              return substr($unicId, -15);
         }
    }

    /**
     * truncate a string only at a whitespace
     */
    function stringTruncate($text, $length, $removePoints = false) {
        $length = abs((int)$length);
        if(strlen($text) > $length) {
            if ($removePoints) {
                $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1', $text);
            } else {
                $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1 ...', $text);
            }

            if(strpos($text, '<p>')) $text .= '</p>';
       }
       return($text);
    }

	/**
	 * Get region collection
	 * @param string $countryCode
	 * @return array
	 */
	public function getRegionCollection($countryCode)
	{
	    $regionCollection = Mage::getModel('directory/region_api')->items($countryCode);
	    return $regionCollection;
	}

    /*
     *
     */
    public function getAllProviderOffers($providerId = false)
    {
        if (!$providerId) {
            $providerId = $this->getCustomer()->getId();
        }

        $collection = Mage::getModel('catalog/product')->getCollection()
                                //->addAttributeToSelect('ofg_customer_id')
                                ->addAttributeToSelect('price')
                                ->addAttributeToSelect('special_price')
                                ->addAttributeToFilter('type_id', 'ofertefurnizori')
                                ->addAttributeToFilter('ofg_customer_id', $providerId);

        if ($collection) {
            return $collection;
        }

        return false;
    }

    /**
     *
     */
    public function countAllOffers($providerId = false)
    {
        $collection = $this->getAllProviderOffers($providerId);

        if ($collection) {
            return $collection->getSize();
        }

        return false;
    }

    public function countActiveOffers($providerId = false)
    {
        $collection = $this->getAllProviderOffers($providerId);
        $collection->addAttributeToFilter('visibility', 4);

        if ($collection) {
            return $collection->getSize();
        }

        return false;
    }

    public function getPriceAvarange($providerId = false)
    {
        $offersCount = $this->countActiveOffers($providerId);

        if ($offersCount) {
            $offers = $this->getAllProviderOffers($providerId)->addAttributeToFilter('visibility', 4);
            $finalPrice = 0;

            foreach ($offers as $offer) {
                $finalPrice += $offer->getFinalPrice();
            }

            $finalPrice /= $offersCount;
            return $finalPrice;
        }

        return 0;
    }

    public function resizeImage($image, $width, $height = false, $media = false)
    {
        if (!$height) $height = $width;

        $mediaDir = Mage::getBaseDir('media') . DS . 'customer';
        $cacheDir = $mediaDir . DS . 'cache' . DS;

        if (file_exists($mediaDir . $image))
        {
            if (!file_exists($cacheDir)) {
                mkdir($cacheDir);
            }

            $_image = new Varien_Image($mediaDir . $image);
            $_image->constrainOnly(true);
            $_image->keepAspectRatio(true);
            $_image->keepFrame(false);
            $_image->keepTransparency(false);
            $_image->resize($width, $height);
            $_image->save($cacheDir . $image);

			if ($media) {
				return Mage::getBaseUrl() . 'media/customer' . DS . 'cache' . $image;
			} else {
				return Mage::getBaseDir('media') . 'customer' . DS . 'cache' . $image;
			}
        }

        return;
    }

    public function seoFriendlyUrl($string)
    {
        $string = str_replace(array('[\', \']'), '', $string);
        $string = preg_replace('/\[.*\]/U', '', $string);
        $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
        //$string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
        $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);

        return strtolower(trim($string, '-'));
    }

    public function timeElapsedString($ptime)
    {
        $currentDateTime = strtotime(date("Y-m-d H:i:s",  strtotime(Mage::getModel('core/date')->date('Y-m-d H:i:s') . ' -1 hour')));
        //echo date('d-m-Y H:i:s', $currentDateTime);
        $ptime = strtotime($ptime);
        $etime = $currentDateTime - $ptime;

        if ($etime < 1)
        {
            return '0 seconds';
        }

        $a = array( 365 * 24 * 60 * 60  =>  'an',
                     30 * 24 * 60 * 60  =>  'luna',
                          24 * 60 * 60  =>  'zi',
                               60 * 60  =>  'ora',
                                    60  =>  'minut',
                                     1  =>  'secunda'
                    );
        $a_plural = array( 'an'   => 'ani',
                           'luna'  => 'luni',
                           'zi'    => 'zile',
                           'ora'   => 'ore',
                           'minut' => 'minute',
                           'secunda' => 'secunde'
                    );

        foreach ($a as $secs => $str)
        {
            $d = $etime / $secs;
            if ($d >= 1)
            {
                $r = round($d);
                return 'acum ' . $r . ' ' . ($r > 1 ? $a_plural[$str] : $str);
            }
        }
    }

    public function getAllMessages()
    {
        $messages = Mage::getModel('conturifurnizori/usermessage')->getCollection()
            ->addFieldToFilter(array('recever_id', 'sender_id'),
                    array(
                        array('recever_id', 'eq' => $this->getCustomer()->getId()),
                        array('sender_id',  'eq' => $this->getCustomer()->getId())
                    )
                )
            ->setOrder('message_id','desc');

        if ($messages) {
            return $messages;
        }

        return false;
    }

    public function getUnreadMessages()
    {
        $messages = $this->getAllMessages()
            ->addFieldToFilter('is_deleted', 0)
            ->addFieldToFilter('is_read', 0)
            ->addFieldToFilter('recever_id', $this->getCustomer()->getId());

        return $messages;
    }

}