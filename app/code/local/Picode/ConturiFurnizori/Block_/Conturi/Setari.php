<?php
class Picode_ConturiFurnizori_Block_Conturi_Setari extends Mage_Core_Block_Template
{
    public function getCustomer()
    {
        if ($this->helper('customer')->isLoggedIn()) {
            // return $customer = $this->helper('customer')->getCustomer();
            return $customer = $this->helper('customer')->getCustomer();
        }
        
        return false;
    }
    
    /*
     * 
     */
    public function getFormUrl($param)
    {
        return Mage::getUrl('conturifurnizori/setari/' . $param . '/save/data/', array('_secure' => true));
    }
    
    public function getAccountOptions($customer, $optionPrefix = false)
	{
	    $accountOptions = array();
        
	    if (!$optionPrefix) {
	        return false;
	    }
        
		if ($customer) {
			foreach ($customer->getData() as $attrCode => $value) {
				if (strpos($attrCode, $optionPrefix) !== false) {
					$accountOptions[$attrCode] = $value;
				}
			}
			
			return $accountOptions;
		}
        
        return;
	}
    
    public function getAllAttributeOptions($attrCode)
    {
        $customerModel = Mage::getModel('customer/customer');
        $attr = $customerModel->getResource()->getAttribute($attrCode);
        if ($attr->usesSource()) {
            return $attr->getSource()->getAllOptions($attrCode);
        }
    }
	
	public function getAttributeLabelByCode($attrCode, $attrValue)
	{
		$customerModel = Mage::getModel('customer/customer');
		$attr = $customerModel->getResource()->getAttribute($attrCode);
        
		if ($attr->usesSource()) {
		    return $attr->getSource()->getOptionText($attrValue);
		}
	}
    
    public function getAttributeDetailsByCode($attrCode)
    {
        $customerModel = Mage::getModel('customer/customer');
        $attr = $customerModel->getResource()->getAttribute($attrCode);
        
        //if ($attr->usesSource()) {
            return $attr;
        //}
    }
	
    public function getStatusDescription($value)
    {
        switch ($value) {
            case '1': // is for Activ
                $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin semper purus urna hendrerit.';
                break;
            case '2': // is for Finalizare plata
                $description = 'Nunc condimentum ante elit, vel suscipit nibh varius sed, eget elementum purus.';
                break;
            case '3': // is for Aprobare
                $description = 'Aliquam lobortis diam ipsum, ac elementum metus hendrerit quis. Vestibulum ante ipsum primis in faucibus.';
                break;
            case '4': // is for Blocat
                $description = 'Phasellus volutpat sem sapien, in convallis neque cursus sed. Mauris in pulvinar nisi.';
                break;
            case '5': // is for Suspendat
                $description = 'Donec id diam faucibus, tincidunt felis vel, ullamcorper velit. Nam tincidunt venenatis tempus.';
                break;
            case '6': // is for Sters
                $description = 'Sed vehicula ipsum tellus, et euismod dui hendrerit nec. Proin porta pulvinar massa non tristique.';
                break;
            default:
                $description = 'No description for the status.';
                break;
        }

        return $description;
    }

    public function getCustomerOrders($customerId = false)
    {
        if (!$customerId) {
            $customerId = $this->getCustomer()->getId();            
        }
        
        $orders = Mage::getModel('sales/order')->getCollection()
                            ->addAttributeToFilter('customer_id', $customerId);
                            
        if ($orders) {
            return $orders;
        }
        
        return false;
    }
	
	public function getYesNo($attrValue)
	{
		return $attrValue ? '<span class="symbol green">&#46;</span>' : '<span class="symbol red">&#39;</span>';
	}
    
    function ajaxUploadImage()
    {
        $form = key($_FILES);
        $name = $_FILES[$form]['name'];
        $size = $_FILES[$form]['size'];
        
        if(strlen($name)) {
            // initial settings
            $customerId = $this->getCustomer()->getId();
            $validFormats = array('jpg', 'png', 'bmp');
            $tmpUploadDir = 'tmp_uploads';
            $maxOriginalSize = 1024 * 1024;
            // define final image sizes
            switch ($form) {
                case 'coverimage':
                    $inputName   = 'coperta';
                    $finalWidth  = 1024;
                    $finalHeight = 353;
                    break;
                case 'logoimage':
                    $inputName   = 'logo';
                    $finalWidth  = 512;
                    $finalHeight = 285;
                    break;
                default:
                    return 'Something went wrong in the form';
                    break;
            }
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
                            case 'image/jpg':
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
                            //case 'bmp':
                                //$image = ImageCreateFromwbmp($tmpFile);
                               // break;
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
                            $tmpMediaDir  = Mage::getBaseUrl('media') . $tmpUploadDir . '/' . $customerId . '/';
                            $response  = '<img src="' . $tmpMediaDir . $newName . '.' . $ext . '" class="' . $form . '-preview">';
                            $response .= '<input type="hidden" name="business_images_' . $inputName . '" value="' . $newName . '.' . $ext . '" />';
                            return $response;
                             
                        } else {
                            
                            return '<div class="error">Imaginea nu a fost uploadata!</div>';
                        }
                        
                    } else {
                        
                        return '<div class="error">Ceva neasteptat s-a intamplat. Te rugam sa incerci din nou.</div>';
                    }
                } else {
                    
                    return '<div class="error">Imaginea e prea mare! (maxim permis 1MB)</div>';
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
    
    

}
