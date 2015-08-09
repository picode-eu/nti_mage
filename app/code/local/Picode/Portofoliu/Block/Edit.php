<?php
class Picode_Portofoliu_Block_Edit extends Mage_Core_Block_Template
{
	private $_customerSession = null;
	private $_customerId = null;
	private $_redirectUrl;
    private $_inputName;

	public function __construct()
	{
		$this->_customerSession = Mage::getSingleton('customer/session');
		$this->_customerId = $this->_customerSession->getCustomer()->getId();
		$this->_redirectUrl = $this->getUrl('customer/account/', array('_secure' => true));
	}

	private function _redirect()
	{
	    return Mage::app()->getFrontController()->getResponse()->setRedirect($this->_redirectUrl);
	}

	public function getAlbumToEdit($albumId)
	{
        $params = $this->getRequest()->getParams();
		$albumDetails = Mage::getModel('portofoliu/albums')->load($albumId);

        if (isset($params['id']) && !$albumDetails->getId()) {
            // album does not exist
            $this->_customerSession->addError('Nu ai permisiunea sa editezi acest album');
            $this->_redirect();
            return;
        } elseif ($albumDetails->getId()) { // edit existing album
            // check if the customer is the owner of the album
            if($this->_customerId == $albumDetails->getCustomerId()){
                return $albumDetails;
            }else{ // is not => redirect to his account w/ error msg
                $this->_customerSession->addError('Nu ai permisiunea sa editezi acest album.');
                $this->_redirect();
                return;
            }
        } else { // add new album
            return;
        }

        return;
	}

	public function getPhotosByAlbumId($id)
	{
		$photosCollection = Mage::getModel('portofoliu/photos')
					->getCollection()
					->addFieldToFilter('album_id', $id)
                    ->setOrder('sort_order', 'ASC');

		if($photosCollection->count()){
			return $photosCollection;
		}

		return $photosCollection;
	}

	public function getVideoToEdit($videoId)
	{
        $params = $this->getRequest()->getParams();
		$videoDetails = Mage::getModel('portofoliu/videos')->load($videoId);

        if (isset($params['id']) && !$videoDetails->getId()) {
            // video does not exist
            $this->_customerSession->addError('Nu ai permisiunea sa editezi acest videoclip');
            $this->_redirect();
            return;
        } elseif ($videoDetails->getId()) { // edit existing video
            // check if the customer is the owner of the video
            if($this->_customerId == $videoDetails->getCustomerId()){
                return $videoDetails;
            }else{ // is not => redirect to his account w/ error msg
                $this->_customerSession->addError('Nu ai permisiunea sa editezi acest videoclip.');
                $this->_redirect();
                return;
            }
        } else { // add new video
            return;
        }

        return;
	}

	public function rebuildVideoUrl($videoDbId, $videoType)
	{
		if($videoType == 'youtube'){
			return 'https://www.youtube.com/watch?v=' . $videoDbId;
		}elseif($videoType == 'vimeo'){
			return 'https://vimeo.com/' . $videoDbId;
		}else{
			return false;
		}
	}

	public function getEmbedCode($urlType, $videoDbId)
	{
		if($urlType == 'youtube'){
			// return '<iframe width="525" height="263" src="//www.youtube.com/embed/'.$videoDbId.'" frameborder="0" allowfullscreen></iframe>';
			return '<iframe width="525" height="350" src="//www.youtube.com/embed/'.$videoDbId.'" frameborder="0" allowfullscreen></iframe>';
		}elseif($urlType == 'vimeo'){
			return '<iframe src="//player.vimeo.com/video/'.$videoDbId.'" width="525" height="350" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
		}
	}

	public function getVideoThumb()
	{
		return 'http://img.youtube.com/vi/'.$this->getVideoToEdit()->getVideoUrl().'/1.jpg';
	}

    public function getAjaxUploadUrl()
    {
        return Mage::getUrl('portofoliu/edit/imageupload/', array('_secure' => true));
    }

	public function getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}

    public function ajaxUploadImage()
    {
        foreach ($_FILES as $form => $file) {
            if ($file['size']) {
                $this->_inputName = $form;
                $name  = $file['name'];
                $type  = $file['type'];
                $size  = $file['size'];
                break;
            }
        }

		if(strlen($name)) {
            // initial settings
            $customerId = $this->getCustomer()->getId();
            $validFormats = array('jpg', 'png');
            $tmpUploadDir = 'tmp_uploads';
            $maxOriginalSize = 1024 * 1024 * 1.5; //1.5 MB
            // define final image sizes
            $finalWidth  = 200;
            $finalHeight = 200;
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
                            case 'image/jpeg':
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

                        $newName = time() . '-' . 'thumb-' . $this->removeSpecialCharacters(reset($nameArr));
                        $fianlImageDest = $path . $newName . '.' . $ext;

                        list($currWidth, $currHeight) = getimagesize($tmpFile);

                        // let's resize / crop the uploaded image
                        if ($this->createThumbImage($image, $quality, $currWidth, $currHeight, $finalWidth, $finalHeight, $fianlImageDest))
                        {
                            $tmpMediaDir  = Mage::getBaseUrl('media') . $tmpUploadDir . '/' . $customerId . '/';
                            $response  = '<img class="product-image" src="' . $tmpMediaDir . $newName . '.' . $ext . '" />';
                            $response .= '<input type="hidden" name="new_thumb_img[]" value="' . $newName . '.' . $ext . '" />';

							// resize the image
							$MaxSize = 900;
							$newName = time() . '-' . $this->removeSpecialCharacters(reset($nameArr));
							$fianlImageDest = $path . $newName . '.' . $ext;

							if ($this->resizeImage($currWidth, $currHeight, $MaxSize, $fianlImageDest, $image, $quality, $type))
							{
							    $response .= '<input type="hidden" name="new_full_img[]" value="' . $newName . '.' . $ext . '" />';
                                $response .= '<span class="button change-img" onclick="changeSelectedImage(this)">Schimba</span>';
                                $response .= '<span class="dlt-img" onclick="deleteSelectedImage(this)">sterge</span>';

                                return $response;
							} else {

							    return '<div class="error">A intervenit o eroare. Imaginea nu a fost uploadata!</div>';
							}

                        } else {

                            return '<div class="error">A intervenit o eroare. Imaginea nu a fost uploadata!</div>';
                        }

                    } else {

                        return '<div class="error">A intervenit o eroare. Te rugam sa incerci din nou.</div>';
                    }
                } else {

                    return '<div class="error">Imaginea e prea mare! (maxim permis 1,5 MB)</div>';
                }
            } else {

                return '<div class="error">Format imagine nepermis! (formate permise: jpg sau png)</div>';
            }

        } else {

            return '<div class="error">Selecteaza o imagine.</div>';
        }

        return;
    }

    public function getInputName()
    {
        return $this->_inputName;
    }

	public function createThumbImage($image, $quality, $currWidth, $currHeight, $finalWidth, $finalHeight, $fianlImageDest)
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
            //ImageDestroy($newThumb);
            //ImageDestroy($image);

            return true;
        }
        return;
    }

	// This function will proportionally resize image
	function resizeImage($CurWidth,$CurHeight,$MaxSize,$DestFolder,$SrcImage,$Quality,$ImageType)
	{
		//Check Image size is not 0
		if($CurWidth <= 0 || $CurHeight <= 0)
		{
			return false;
		}

		//Construct a proportional size of new image
		$ImageScale      	= min($MaxSize / $CurWidth, $MaxSize / $CurHeight);
		$NewWidth  			= ceil($ImageScale * $CurWidth);
		$NewHeight 			= ceil($ImageScale * $CurHeight);

		if($CurWidth < $NewWidth || $CurHeight < $NewHeight)
		{
			$NewWidth = $CurWidth;
			$NewHeight = $CurHeight;
		}
		$NewCanves 	= imagecreatetruecolor($NewWidth, $NewHeight);
		// Resize Image
		if(imagecopyresampled($NewCanves, $SrcImage, 0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight))
		{
			switch(strtolower($ImageType))
			{
				case 'image/png':
					imagepng($NewCanves, $DestFolder);
					break;
				// case 'image/gif':
					// imagegif($NewCanves,$DestFolder);
					// break;
				case 'image/jpeg':
				case 'image/pjpeg':
					imagejpeg($NewCanves, $DestFolder, $Quality);
					break;
				default:
					return false;
			}

			//Destroy image, frees up memory
			if(is_resource($NewCanves)) {
				imagedestroy($NewCanves);
				imagedestroy($SrcImage);
			}

		return true;

		}

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