<?php
class Picode_ConturiFurnizori_OferteController extends Mage_Core_Controller_Front_Action
{
	/**
     * Check if customer is logged in or not
     * If not logged in then redirect to customer login
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    public function getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

	public function listAction()
	{
	    if ($this->getRequest()->getParam('type')) {
	        $this->loadLayout();

            $this->_initLayoutMessages('customer/session');
            $this->_initLayoutMessages('catalog/session');

            $this->getLayout()->getBlock('head')->setTitle($this->__('Oferte foto/video'));
            $this->renderLayout();
	    } else {
	        $this->_redirect('customer/account/');
	    }
	}

    public function editeazaAction()
    {
        $productId = $this->getRequest()->getParam('oferta');

        if ($productId != 'noua') {
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $_product = Mage::getModel('catalog/product')->load($productId);
            $productOwner = $_product->getOfgCustomerId();

            // verify that the customer has permission to edit the given product
            if ($customerId != $productOwner) {
                // redirect with error message
                $message = 'Nu ai permisiunea să editezi această ofertă!';
                Mage::getSingleton('core/session')->addError($message);
                $this->_redirect('customer/account/');

                return;

            } else {
                // the logged in customer is the product owner
                Mage::getSingleton('customer/session')->setData('current_status', $_product->getStatus());

                $this->loadLayout();

                $this->_initLayoutMessages('customer/session');
                $this->_initLayoutMessages('catalog/session');

                $this->getLayout()->getBlock('head')->setTitle($this->__('Editeaza oferta'));
                $this->renderLayout();
            }
        } elseif ($productId == 'noua') {
            // create new product
            Mage::getSingleton('customer/session')->setData('current_status', 2);
            $this->loadLayout();

            $this->_initLayoutMessages('customer/session');
            $this->_initLayoutMessages('catalog/session');

            $this->getLayout()->getBlock('head')->setTitle($this->__('Creaza oferta noua'));
            $this->renderLayout();
        } else {
            // redirect with error message
            $message = 'Nu ai permisiunea sa editezi aceasta oferta!';
            Mage::getSingleton('core/session')->addError($message);
            $this->_redirect('customer/account/');
            return;
        }

        return;
    }

    public function imageuploadAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_redirect('customer/account');
        }

        return;
    }

    public function ajaxformAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_redirect('customer/account');
        }

        return;
    }

    public function saveofertaAction()
    {
        /**
         * Modern versions of Magento won't let you programmatically save a product
         * if the store object's ID isn't set to the admin store's id
         */

		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

    	// inputs that needs special treatments
    	$specialFormatedInputs = array(
                                'price',
                                'special_price',
                                'special_from_date',
                                'special_to_date',
                                'ofg_zona_personalizata',
                                'delete_orig_image',
                                'main_img',
                                'addimg_1',
                                'addimg_2',
                                'addimg_3',
                            );

        // inputs that needs further verification before save
        $specialAttentionInputs = array(
                                'visibility',
                            );

        // required inputs that are not prezents in form
        $systemRequiredInputs = array(
                                'short_description',
                                'status',
                                'ofg_customer_frontname',
                                'ofg_customer_id',
                                'tax_class_id',
                                'meta_title',
                                'meta_description',
                            );

        $existing = false;
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $helper = Mage::helper('conturifurnizori');
        $ofertaData = $helper->getOfertaObj();
        //Zend_Debug::dump($ofertaData->getData());

        if ($ofertaData->hasData()) {
            // temp directory of uploaded images
            $tempDir = Mage::getBaseDir() . DS . 'media' . DS .'tmp_uploads' . DS . $customer->getId() . DS;
            // load existing product or create a new one
            if ($ofertaData['sku'] != '') {
                // existing product
                $existing = true;
                $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $ofertaData['sku']);
                //die('existing');
            } else {
                // new product
                $product = Mage::getModel('catalog/product');
                $ofertaData->setData('sku', $customer->getId() . '-' . $helper->generateUnicKey());
				// set defaults product data
				$product->setWebsiteIds(array(1));
				$product->setStoreId(1);
				$product->setAttributeSetId(25);// 25 is for oferte Furnizori
				$product->setTypeId('ofertefurnizori');
                $product->setData('sku', $ofertaData['sku']);
				$product->setStockData(array('is_in_stock' => 1,  'qty' => 0));
				$product->setInventoryManageStock(0);
            }

			// save url key
			$product->setData('url_key', $this->getFriendlyUrl($ofertaData['name']) . '-' . $product->getSku());
			$product->setData('url_path', $this->getFriendlyUrl($ofertaData['name']) . '-' . $product->getSku());
            $product->setData('url_key_create_redirect', true);

            // start adding product attributes
            foreach ($ofertaData->getData() as $attrCode => $value) {
                if (in_array($attrCode, $specialFormatedInputs)) {
                    // format and save special inputs
                    switch ($attrCode) {
                        case 'price':
                        case 'special_price':
                            $product->setData($attrCode, (int)$value);
                            break;
                        case 'ofg_zona_personalizata':
							$personalizedZones = implode(',', $value);
							$product->setData($attrCode, $personalizedZones);
                            break;
                        case 'special_from_date':
                        case 'special_to_date':
                            $formatedDate = date('Y-m-d', strtotime($value));
                            $product->setData($attrCode, $formatedDate);
                            break;
                        case 'main_img':
                        case 'addimg_1':
                        case 'addimg_2':
                        case 'addimg_3':
                            // do nothing for the moment
                            break;
                        case 'delete_orig_image':
							// do nothing for the moment
                            break;
                    }
                } elseif (in_array($attrCode, $specialAttentionInputs)) {
                    switch ($attrCode) {
                        //echo $attrCode . '<br />';
                        case 'visibility':
                            $product->setData('visibility', $ofertaData['visibility']);
                            break;
                    }
                } else {
                    //Zend_Debug::dump($attrCode . '-' . $value);
                    $product->setData($attrCode, $value);
                }
            }

            // save inputs that are not prezent in the form
            foreach ($systemRequiredInputs as $code) {
                switch ($code) {
                    case 'short_description':
                    case 'meta_description':
                        $description = $helper->stringTruncate($ofertaData['description'], 255);
                        $product->setData($code, $description);
                        break;
                    case 'status':
                        if (!$this->canActivateOffers($customer->getId())) {
                            // check if provider statuses are activ
                            $message = 'Statusul contului tău este altul decât activ sau vizibilitatea este privată. Oferta a fost salvată dar <strong>nu va apărea în liste</strong> până la activarea contului.';
                            Mage::getSingleton('core/session')->addNotice($message);
                            $product->setData($code, 2);
                            $product->setData('visibility', 1);
                        } elseif ($this->activeOffersExceeded($customer->getId(), $existing, $product->getStatus())) {
                            $accountName = Mage::helper('conturifurnizori')->getAccountTypeNameBylLevel('cont_level', $customer->getFurnizorAccountLevel())->getName();
                            $message = 'Opțiunile contului tău <strong>' . $accountName . '</strong> îți permite să ai maxim ' . $customer->getAcOpMaxOferteActive() . ' oferte active. Oferta a fost salvată dar vizibilitatea ei a rămas <strong>privată</strong>.';
                            Mage::getSingleton('core/session')->addNotice($message);
                            $product->setData($code, 2);
                            $product->setData('visibility', 1);
                        } else {
                            $product->setData($code, 1);
                        }

                        break;
                    case 'ofg_customer_frontname':
                        //$product->setData($code, $customer->getFirstname() . ' ' . $customer->getLastname());
                        $product->setData($code, $customer->getBusinessDescriptionsTitle());
                        break;
                    case 'ofg_customer_id':
                        $product->setData($code, $customer->getId());
                        break;
                    case 'tax_class_id':
                        $product->setData($code, 2);
                        break;
                    case 'meta_title':
                        $product->setData($code, 'Oferta ' . $ofertaData['name']);
                        break;
                }
            }

            //Zend_Debug::dump($product->getData());

            // reset unused inputs
            switch ($ofertaData['ofg_tip_oferta']) {
                case '1': // foto
                    // unset video details
                    $attributes = $helper->getAttributesFromGroup(180); // 180 is for Video Details
                    $categories = array('4', '6');
                    break;

                case '2': // video
                    // unset foto inputs
                    $attributes = $helper->getAttributesFromGroup(181); // 181 is for Foto Details
                    $categories = array('4', '7');
                    break;

                case '3': // foto-video
                    $attributes = false;
					$categories = array('4', '8');
                    break;
            }

            if ($attributes) {
                foreach ($attributes as $attr) {
                    $product->setData($attr->getAttributeCode(), $attr->getDefaultValue());
                }
            }

			// asign categories
			$product->setCategoryIds(array());
			$product->setCategoryIds($categories);

			try
			{
			    $product->setCreatedAt(date("Y-m-d H:i:s",  strtotime(Mage::getModel('core/date')->date('Y-m-d H:i:s') . ' +2 hour')));

                // set popularity form provider's reputation
                // $providerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
                // $customerReputation = Mage::getModel('providerreputation/reputation')
                        // ->getCollection()
                        // ->addFieldToFilter('provider_id', $providerId);
//
                // Zend_Debug::dump($providerReputation->getData()); die();

			    $product->save();

                // fire an event
                if (!$existing) {
                    Mage::dispatchEvent('offer_save_after', array('offer' => $product));
                }

				// save / delete product images
				if (
					isset($ofertaData['main_img']) ||
					isset($ofertaData['addimg_1']) ||
					isset($ofertaData['addimg_2']) ||
					isset($ofertaData['addimg_3']) ||
					isset($ofertaData['delete_orig_image'])
					)
				{
					$this->saveProductImage($product, $ofertaData, $existing);
				}

                if ($existing) {
                    $message = 'Modificarile la oferta <strong>' . $product->getName() . '</strong> au fost salvate cu succes.';
                } else {
                    $message = 'Oferta <strong>' . $product->getName() . '</strong> a fost salvata cu succes.';
                }

				// reindex Category Products
                //$process = Mage::getModel('index/indexer')->getProcessByCode('catalog_category_product')->reindexAll();

				Mage::getSingleton('core/session')->addSuccess($message);
				// redirect to customer account
		        $this->_redirect('customer/account/');

				return;
			}
			catch (Exception $e)
			{
			    //throw new Exception( 'Something really gone wrong' . $e->getMessage());
			    $message = 'Ceva neasteptat s-a intamplat. Te rugam incearca din nou.';
	            Mage::getSingleton('core/session')->addError($message);
	            $this->_redirect('customer/account/');

				return;
			}

        } else {
            $message = 'Ceva neasteptat s-a intamplat. Te rugam incearca din nou. Daca problema persista nu ezita sa ne contactezi.';
            Mage::getSingleton('core/session')->addError($message);
            $this->_redirect('customer/account/');

            return;
        }

		// ends editing the product
		return;
    }

    public function deleteofertaAction()
    {
        $params = $this->getRequest()->getParams();
        $customerId = $this->getCustomerSession()->getCustomerId();
        $_product = Mage::getModel('catalog/product')->load($params['id']);

        if ($customerId != $_product->getOfgCustomerId()) {
            $message = 'Nu ai permisiunea sa editezi aceasta oferta!';
            Mage::getSingleton('core/session')->addError($message);
            $this->_redirect('customer/account/');

            return;
        } else {
            try
            {
                /**
                 * Modern versions of Magento won't let you programmatically save a product
                 * if the store object's ID isn't set to the admin store's id
                 */
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

                // remove product images
                $productImages = $_product->getMediaGallery('images');
                foreach ($productImages as $image) {
                    $imageFile = Mage::getBaseDir() . '/media/catalog/product' . $image['file'];
                    if (file_exists($imageFile)) {
                        unlink($imageFile);
                    }
                }
                // delete the product
                $_product->delete();

            }
            catch (Exception $e)
            {
                //throw new Exception( 'Something really gone wrong' . $e->getMessage());
                $message = 'Ceva neasteptat s-a intamplat. Te rugam incearca din nou.';
                Mage::getSingleton('core/session')->addError($message);
                $this->_redirect('customer/account/');

                return;
            }
        }

        $message = 'Oferta "' . $_product->getName() . '" a fost stearsa cu succes.';
        Mage::getSingleton('core/session')->addSuccess($message);
        $this->_redirect('customer/account/');

        return;
    }

    public function canActivateOffers($providerId)
    {
        $_provider = Mage::getModel('customer/customer')->load($providerId);

        if ($_provider->getFurnizorAccountStatus() == 1 && $_provider->getFurnizorAccountOnlineStatus() == 1) {
            return true;
        }

        return false;
    }

    public function activeOffersExceeded($providerId, $existing, $newStatus)
    {
        $_provider = Mage::getModel('customer/customer')->load($providerId);
        $activeOffersAllowed = $_provider->getAcOpMaxOferteActive();
        $activeOffers = Mage::helper('conturifurnizori')->countActiveOffers($providerId);
        $currentStatus = Mage::getSingleton('customer/session')->getCurrentStatus();

        if ($existing && $newStatus < $currentStatus) {
            $activeOffers += 1;
        } else {
            $activeOffers -= 1;
        }

        //echo $newStatus . ' - ' . $activeOffers; die();

        if ($activeOffers < $activeOffersAllowed) {
            return false;
        }

        return true;
    }

    public function saveProductImage($product, $ofertaData, $existing)
    {
        $reloadImgObj = false;
        $mediaApi  = Mage::getModel("catalog/product_attribute_media_api");
        $mediaPath = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS;

        if ($product->getId() && $existing) {
            $items = $mediaApi->items($product->getId());

            // before save the new images we have first to delete the old ones
            if (count($ofertaData['delete_orig_image'])) {
                //Zend_Debug::dump($items); die();

                // create an array with the position of the images that will be deleted
                foreach ($ofertaData['delete_orig_image'] as $image) {
                    $position = explode('_', $image);
                    if (end($position) == 'img') {
                        $deletePosition[] = 1;
                    } else {
                        /*
                         * We have to increment the position with 1
                         * because the counter of the deleted image starts from 1
                         * Position 1 is reserved for the main image
                         */
                        $deletePosition[] = end($position) + 1;
                    }
                }

                // start deleting images
                foreach ($items as $item) {
                    if (in_array($item['position'], $deletePosition)) {

                        $mediaApi->remove($product->getId(), $item['file']);
                        if (file_exists($mediaPath . $item['file'])) {
                            unlink($mediaPath . $item['file']);
                        }

                        $reloadImgObj = true;
                    }
                }
            }

            // reload image object
            if ($reloadImgObj) {
                $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
            }

        }

        // get all product images if product exists (is not new)
        if ($product->getId() && $existing) {
            $items = $mediaApi->items($product->getId());
            //Zend_Debug::dump($items); die();
        }

        // start saving new uploaded images
        $tmpImagePath = Mage::getBaseDir('media') . DS . 'tmp_uploads' . DS . $this->getCustomerSession()->getCustomer()->getId() . DS;

        // save new main image
        if (isset($ofertaData['main_img'])) {

            $imgName = $tmpImagePath . $ofertaData['main_img'];
            $pathInfo = pathinfo($imgName);

            switch($pathInfo['extension']){
                case 'png':
                    $mimeType = 'image/png';
                    break;
                case 'jpg':
                    $mimeType = 'image/jpeg';
                    break;
            }

            $newImage = array(
                'file' => array(
                    'content' => base64_encode($imgName),
                    'mime' => $mimeType,
                    'name' => basename($imgName),
                    ),
                'label' => '',
                'position' => 1,
                'types' => array ('image','small_image','thumbnail'),
                'exclude' => 0,
            );

            $mediaApi->create($product->getSku(), $newImage, null, 'sku');
        }

        // upload other gallery images
        for ($i = 1; $i <= 3; $i++) {
            if (isset($ofertaData['addimg_' . $i])) {
                $imgName = $tmpImagePath . $ofertaData['addimg_' . $i];
                $pathInfo = pathinfo($imgName);

                switch($pathInfo['extension']){
                    case 'png':
                        $mimeType = 'image/png';
                        break;
                    case 'jpg':
                        $mimeType = 'image/jpeg';
                        break;
                }

                $newImage = array(
                    'file' => array(
                        'content' => base64_encode($imgName),
                        'mime' => $mimeType,
                        'name' => basename($imgName),
                        ),
                    'label' => '',
                    'position' => $i + 1,
                    'types' => 'gallery',
                    'exclude' => 0,
                );

                $mediaApi->create($product->getSku(), $newImage, null, 'sku');
            }
        }
    }

	function getFriendlyUrl($string, $ext = false)
	 {
	      $string = str_replace(array('[\', \']'), '', $string);
	      $string = preg_replace('/\[*\]/U', '', $string);
	      $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
	      $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
	      $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
	      $string = trim($string, '-');

	      if($ext){
	           return strtolower(trim($string, '-')).$ext;
	      }else{
	           return strtolower(trim($string, '-'));
	      }
	 }



    /*
    array(51) {
      ["name"] => string(6) "Name 1"
      ["ofg_tip_oferta"] => string(10) "foto-video"
      ["description"] => string(11) "Description"
      ["visibility"] => string(1) "4"
      ["price"] => string(3) "120"
      ["ofg_zona_personalizata"] => array(3) {
        [0] => string(3) "278"
        [1] => string(3) "284"
        [2] => string(3) "290"
      }
      ["oferta_speciala"] => string(1) "1"
      ["special_price"] => string(3) "325"
      ["special_from_date"] => string(10) "09.09.2014"
      ["special_to_date"] => string(10) "30.09.2014"
      ["addimg_1"] => string(23) "1410233272-checkout.jpg"
      ["delete_orig_image"] => array(1) {
        [0] => string(8) "addimg_2"
      }
      ["ofg_nr_fotografi"] => string(1) "1"
      ["ofg_nr_cameramani"] => string(1) "1"
      ["ofg_cheltuieli_transport"] => string(7) "incluse"
      ["ofg_cheltuieli_cazare"] => string(9) "decontate"
      ["ofg_disponibilitate"] => string(6) "max-14"
      ["ofg_pregatiri_nunta"] => string(1) "1"
      ["ofg_cununie_religioasa"] => string(1) "1"
      ["ofg_cununie_civila_alta_zi"] => string(1) "1"
      ["ofg_restaurant"] => string(1) "1"
      ["ofg_restaurant_panala"] => string(10) "alt-moment"
      ["ofg_restaurant_definit"] => string(10) "alt moment"
      ["ofg_nr_sedinte"] => string(1) "4"
      ["ofg_sedinta_logodna"] => string(1) "0"
      ["ofg_sedinte_inainte_biserica"] => string(1) "1"
      ["ofg_sedinte_dupa_biserica"] => string(1) "1"
      ["ofg_trash_the_dress"] => string(1) "1"
      ["ofg_sedinte_suplimentara"] => string(20) "sedinta 1, sedinta 2"
      ["off_dvd"] => string(1) "1"
      ["off_dvd_nrcopii"] => string(1) "1"
      ["off_dvd_nr_img"] => string(1) "1"
      ["off_slide_show"] => string(1) "1"
      ["off_slide_show_detalii"] => string(30) "Detalii Slide Show (descriere)"
      ["off_album_clasic"] => string(1) "1"
      ["off_album_clasic_cant"] => string(1) "1"
      ["off_album_clasic_detalii"] => string(30) "Detalii Slide Show (descriere)"
      ["off_album_carte"] => string(1) "1"
      ["off_album_carte_cant"] => string(1) "1"
      ["off_album_carte_detalii"] => string(36) "Detalii Album foto-carte (descriere)"
      ["ofv_format_filmare"] => string(10) "alt-format"
      ["ofv_format_definit"] => string(10) "alt format"
      ["ofv_montaj_dvd"] => string(1) "1"
      ["ofv_montaj_dvd_nrcopii"] => string(1) "3"
      ["ofv_montaj_blu_ray"] => string(1) "1"
      ["ofv_montaj_blu_ray_nrcopii"] => string(1) "2"
      ["ofv_montaj_film"] => string(1) "1"
      ["ofv_montaj_durata"] => string(1) "3"
      ["ofv_videoclip"] => string(1) "1"
      ["ofv_videoclip_durata"] => string(2) "20"
      ["ofv_detalii_suplimentare"] => string(28) "Detalii suplimentare filmare"
    }
    */
}
