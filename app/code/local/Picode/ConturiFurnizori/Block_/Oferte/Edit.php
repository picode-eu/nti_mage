<?php
class Picode_ConturiFurnizori_Block_Oferte_Edit extends Mage_Core_Block_Template
{
    protected $_product;
    protected $_customer;
	protected $_customerSession;
	protected $_allowedAttr;
    protected $_specialInputs;

    public function __construct()
    {
        $paramOferta = $this->getRequest()->getParam('oferta');
        switch ($paramOferta) {
            case 'noua':
                $this->_product = Mage::getModel('catalog/product');
                break;

            default:
                $this->_product = Mage::getModel('catalog/product')->load($paramOferta);
                break;
        }

		$this->_customerSession = Mage::getSingleton('customer/session');
        $this->_customer = $this->_customerSession->getCustomer();

		$this->_allowedAttr = array(
								// text
								'name',
								'price',
								'special_price',
								'special_from_date',
								'special_to_date',
								'ofg_restaurant_definit',
								'ofg_sedinte_suplimentara',
								'off_dvd_nr_img',
								'ofv_format_definit',
								'ofv_videoclip_durata',
								// select
								'ofg_tip_oferta',
								'visibility',
								'oferta_speciala',
								'ofg_cheltuieli_transport',
								'ofg_cheltuieli_cazare',
								'ofg_nr_fotografi',
								'ofg_nr_cameramani',
								'ofg_disponibilitate',
								'ofg_pregatiri_nunta',
								'ofg_cununie_civila',
								'ofg_cununie_civila_alta_zi',
								'ofg_cununie_religioasa',
								'ofg_restaurant',
								'ofg_restaurant_panala',
								'ofg_nr_sedinte',
								'ofg_sedinta_logodna',
								'ofg_sedinte_inainte_biserica',
								'ofg_sedinte_dupa_biserica',
								'ofg_trash_the_dress',
								'off_dvd',
								'off_dvd_nrcopii',
								'off_slide_show',
								'off_album_clasic',
								'off_album_clasic_cant',
								'off_album_carte',
								'off_album_carte_cant',
								'ofv_format_filmare',
								'ofv_montaj_dvd',
								'ofv_montaj_dvd_nrcopii',
								'ofv_montaj_blu_ray',
								'ofv_montaj_blu_ray_nrcopii',
								'ofv_montaj_film',
								'ofv_montaj_durata',
								'ofv_videoclip',
								// textarea
								'description',
								'off_slide_show_detalii',
								'off_album_clasic_detalii',
								'off_album_carte_detalii',
								'ofv_detalii_suplimentare',
								// checkbox
								'ofg_zona_personalizata',
								// image
								'delete_orig_image',
								'main_img',
								'addimg_1',
								'addimg_2',
								'addimg_3',
								// system
								'form_key',
								'sku',
							 );

    $this->_unrequiredInputs = array(
                                'sku',
                                'main_img',
                                'ofg_sedinte_suplimentara',
                                'ofv_detalii_suplimentare',
                                );
    }

    public function getPageTitle()
    {
        if ($this->_product->getId()) {
            return 'Editeaza oferta "' . $this->_product->getName() . '"';
        } else {
            return 'Adauga oferta noua';
        }
    }

    public function getProductForEdit()
    {
        return $this->_product;
    }

    public function getCustomer()
    {
        return $this->_customer;
    }

    public function goBack()
    {
        return Mage::getUrl('customer/account/', array('_secure' => true));
    }

    public function getAjaxUploadUrl()
    {
        return Mage::getUrl('conturifurnizori/oferte/imageupload/', array('_secure' => true));
    }

    public function getBaseFormUrl()
    {
        return Mage::getUrl('conturifurnizori/oferte/', array('_secure' => true));
    }

    public function getAjaxFormAction()
    {
        return Mage::getUrl('conturifurnizori/oferte/ajaxform/', array('_secure' => true));
    }

    public function getSaveFormAction()
    {
        return Mage::getUrl('conturifurnizori/oferte/saveoferta/', array('_secure' => true));
    }

    public function fotoIsHidden($servicii)
    {
        if ($servicii == '2') { // video
            return 'hidden';
        } else {
            return false;
        }
    }

    public function videoIsHidden($servicii)
    {
        if ($servicii == '1') { // foto
            return 'hidden';
        } else {
            return false;
        }
    }

    public function ajaxUploadImage()
    {
        foreach ($_FILES as $form => $file) {
            if ($file['size']) {
                $input = $form;
                $name  = $file['name'];
                $type  = $file['type'];
                $size  = $file['size'];
                break;
            }
        }

        if(strlen($name)) {
            // initial settings
            $customerId = $this->getCustomer()->getId();
            $validFormats = array('jpg', 'png', 'bmp');
            $tmpUploadDir = 'tmp_uploads';
            $maxOriginalSize = 1024 * 1024 * 5;
            // define final image sizes
            $finalWidth  = 450;
            $finalHeight = 450;
            // define final image quality
            $quality = 100;

            // create temporary upload directory
            $path = Mage::getBaseDir('media') .  DS . $tmpUploadDir . DS;
            if (!is_dir($path)) mkdir($path);
            // create temporary customer directory
            $path .= $customerId . DS;
            if (!is_dir($path)) mkdir($path);
            // get file name and size
            $name = $_FILES[$form]['name'];
            $size = $_FILES[$form]['size'];
            $type = $_FILES[$form]['type'];
            // extract file extension from name
            $nameArr = explode('.', $name);
            $ext     = end($nameArr);

            if(in_array(strtolower($ext), $validFormats)) { // validate file format

                if($size < $maxOriginalSize) { // check file size

                    $tmpFile = $_FILES[$form]['tmp_name'];

                    if(file_exists($tmpFile)){ // check uploaded file

                        switch(strtolower($type))
                        {
                            case 'image/jpeg':
                            case 'image/pjpeg':
                                $image = imagecreatefromjpeg($tmpFile);
                                break;
                            case 'image/png':
                                $image =  imagecreatefrompng($tmpFile);
                                break;
                            // case 'image/gif':
                                // $image =  imagecreatefromgif($tmpFile);
                                // break;
                            // case 'bmp':
                                // $image = ImageCreateFromwbmp($tmpFile);
                            default:
                                return 'Invalid file format'; //output error and exit
                        }

                        $newName = time() . '-' . $this->removeSpecialCharacters(reset($nameArr));
                        $fianlImageDest = $path . $newName . '.' . $ext;
                        list($currWidth, $currHeight) = getimagesize($tmpFile);

                        // let's resize / crop the uploaded image
                        if ($this->cropOrResizeImage($image, $quality, $currWidth, $currHeight, $finalWidth, $finalHeight, $fianlImageDest))
                        {
                            //list($resizedWidth, $resizedHeight) = getimagesize($fianlImageDest);
                            // $tmpMediaDir  = Mage::getBaseUrl('media') . $tmpUploadDir . '/' . $customerId . '/';
                            // $response  = '<img src="' . $tmpMediaDir . $newName . '.' . $ext . '" class="' . $form . '-preview">';
                            // $response .= '<input type="hidden" name="business_images_' . $input . '" value="' . $newName . '.' . $ext . '" />';
                            // return $response;

                            $tmpMediaDir  = Mage::getBaseUrl('media') . $tmpUploadDir . '/' . $customerId . '/';
                            $response  = '<img class="product-image" src="' . $tmpMediaDir . $newName . '.' . $ext . '" />';
                            // $response .= '<span id="' . $input . '" class="button new-image">';
                            // $response .= 'Modifica';
                            // $response .= '</span>';
                            $response .= '<input type="hidden" name="' . $input . '" value="' . $newName . '.' . $ext . '" />';
							$response .= '<input type="hidden" class="delete-image-input" name="delete_orig_image[]" value="' . $input . '">';
							//$response .= '<span class="delete-image" id="delete_' . $input . '">Renunta</span>';

                            return $response;

                        } else {

                            return '<div class="error">Imaginea nu a fost uploadata!</div>';
                        }

                    } else {

                        return '<div class="error">Ceva neasteptat s-a intamplat. Te rugam sa incerci din nou.</div>';
                    }
                } else {

                    return '<div class="error">Imaginea e prea mare! (maxim permis 5MB)</div>';
                }
            } else {

                return '<div class="error">Format imagine nepermis! (fisiere permise: jpg, png sau bmp)</div>';
            }

        } else {

            return '<div class="error">Selecteaza o imagine.</div>';
        }

        return;
    }

    public function cropOrResizeImage($image, $quality, $currWidth, $currHeight, $finalWidth, $finalHeight, $fianlImageDest)
    {
        if ($image) {
            // check if ratios match
            $_ratio = array($currWidth / $currHeight, $finalWidth / $finalHeight);

            if ($_ratio[0] !=  $_ratio[1]) { // crop image
                // find the right scale to use
                $_scale = min((float)($currWidth / $finalWidth),(float)($currHeight / $finalHeight));

                // coords to crop
                $cropX = (float)($currWidth - ($_scale * $finalWidth));
                $cropY = (float)($currHeight - ($_scale * $finalHeight));

                // cropped image size
                $cropW = (float)($currWidth - $cropX);
                $cropH = (float)($currHeight - $cropY);

                $crop = ImageCreateTrueColor($cropW, $cropH);
                // crop the middle part of the image to fit proportions
                ImageCopy($crop, $image, 0, 0, (int)($cropX / 2), (int)($cropY / 2), $cropW, $cropH);
            }

            // do the thumbnail
            $newThumb = ImageCreateTrueColor($finalWidth, $finalHeight);
            if (isset($crop)) { // been cropped
                ImageCopyResampled($newThumb, $crop, 0, 0, 0, 0, $finalWidth, $finalHeight, $cropW, $cropH );
                ImageDestroy($crop);
            } else { // ratio match, regular resize
                ImageCopyResampled($newThumb, $image, 0, 0, 0, 0, $finalWidth, $finalHeight, $currWidth, $currHeight );
            }

            ImageJpeg($newThumb, $fianlImageDest, $quality);
            ImageDestroy($newThumb);
            ImageDestroy($image);

            return true;
        }
        return;
    }

    public function removeSpecialCharacters($string = false)
    {
        if ($string) {
            $string = str_replace(array('_'), '-', $string);
            $string = str_replace(array('[\', \']'), '', $string);
            $string = preg_replace('/\[.*\]/U', '', $string);
            $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
            //$string = htmlentities($string, ENT_COMPAT, 'utf-8');
            $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
            $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
            $string = strtolower(trim($string, '-'));
        }

        return $string;
    }

    public function getAllAttributeOptions($attrCode)
    {
        $customerModel = Mage::getModel('customer/customer');
        $attr = $customerModel->getResource()->getAttribute($attrCode);
        if ($attr->usesSource()) {
            return $attr->getSource()->getAllOptions($attrCode);
        }
    }

    public function isRequiredLabel($product, $relatedAttr, $relatedValue)
    {
         if ($product->getData($relatedAttr) == $relatedValue) {
            return 'required';
        } else {
            return '';
        }

        return;
    }

    public function getLabelAsterix($product, $relatedAttr, $relatedValue)
    {
        if ($product->getData($relatedAttr) != $relatedValue) {
            return '*';
        } else {
            return '';
        }

        return;
    }

    public function isRequired($product, $relatedAttr, $relatedValue, $inputType)
    {
        if ($product->getData($relatedAttr) == $relatedValue) {
            if ($inputType == 'text' || $inputType == 'textarea') {
                return 'required-entry';
            } else {
                return 'validate-select';
            }
        } else {
            return 'disabled';
        }

        return;

    }

    public function isDisabled($product, $relatedAttr, $relatedValue)
    {
        if ($product->getData($relatedAttr) != $relatedValue) {
            return 'disabled="disabled"';
        } else {
            return '';
        }

        return;
    }

    /**
     * save oferta data (ajax)
     * create/update oferta object
     * @return string success or error
     */
    public function storeFormData()
    {
        $params = $this->getRequest()->getParams();
		$clearedParams = $this->clearParams($params);
		//$allowedAttr = $this->_allowedAttr;
		$nextStep = isset($params['next_step']) && $params['next_step'] != '' ? $params['next_step'] : 'last_step';
		$error = false;

		// get existing data from object or creat a new one
		if (isset($params['first_step'])) {
		    $ofertaData = new Varien_Object();
		} else {
		    $ofertaData = $this->helper('conturifurnizori')->getOfertaObj();
		}

		// set form data to object
		foreach ($clearedParams as $key => $val) {
		    //echo $key . '=' . $val . "\n";
			if (in_array($key, $this->_allowedAttr)) {
			    // look for unrequired inputs
			    if (!in_array($key, $this->_unrequiredInputs) && $val == '') {
			        $error = true;
                    break;
			    } else {
                    //echo $key . '=' . $val . "\n";
			        $ofertaData->setData($key, $val);
			    }
			} else {
				$error = true;
				break;
			}
		}


        if (!$error ) {
            //Zend_Debug::dump($ofertaData->getData()); die();
        	// save data in session
			$ofertaData = serialize($ofertaData);
			$this->_customerSession->setData('oferta_object', $ofertaData);
            // return success and next step
            //Zend_Debug::dump($this->_customerSession->getOfertaObject()); die();
            return $nextStep;
        } else {
        	return 'error';
        }

    }

	public function clearParams($params = false)
	{
		$systemInputs = array(
		                  'form_key',
		                  'first_step',
		                  'next_step',
		                  'last_step',
		                  'ofg_zona_personalizata_hidden'
                        );

		$clearedParams = array();

		if ($params) {
			foreach ($params as $key => $val) {
				//echo $key . '!<br />';
				if (in_array($key, $systemInputs)) {
					unset($key);
				} else {
					if (is_array($val)) {
						foreach ($val as $k => $v) {
							$clearedParams[$key][$k] = strip_tags(trim($v));
						}
					} else {
						$clearedParams[$key] = strip_tags(trim($val));
					}
				}
			}

			return $clearedParams;
		} else {
			return false;
		}

		return;
	}























}
