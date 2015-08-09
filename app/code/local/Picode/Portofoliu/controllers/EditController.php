<?php
class Picode_Portofoliu_EditController extends Mage_Core_Controller_Front_Action
{
	private $_registeredParams = array(
	                        // album
							'album_id',
							'cover_img',
							'form_key',
							'album_name',
							'is_visible',
							'album_description',
							'new_thumb_img',
							'new_full_img',
							'delete_existing',
							'photo_order',
							// video
							'video_id',
							'video_name',
							'video_description',
							'video_url',
						);

    private $_videoType;

	private function customerSession()
	{
		return Mage::getSingleton('customer/session');
	}

	public function stripTags($str)
	{
		return strip_tags($str);
	}

	private function albumModel()
	{
		return Mage::getModel('portofoliu/albums');
	}

	private function videoModel()
	{
		return Mage::getModel('portofoliu/videos');
	}

	/**
	* Checking if user is logged in or not
	* If not logged in then redirect to customer login
	*/
	public function preDispatch()
	{
	    parent::preDispatch();
	    if (!$this->customerSession()->authenticate($this)){
	        $this->setFlag('', 'no-dispatch', true);
	    }
	}

	public function indexAction()
	{
		$this->loadLayout();

        $this->_initLayoutMessages('customer/session');
		$this->getLayout()->getBlock("head")->setTitle($this->__("Portofoliu foto / video"));

		$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
		$breadcrumbs->addCrumb("home", array(
			"label" => $this->__("Home Page"),
			"title" => $this->__("Home Page"),
			"link"  => Mage::getBaseUrl()
		));

		$breadcrumbs->addCrumb("portofoliu foto-video", array(
			"label" => $this->__("Portofoliu foto-video"),
			"title" => $this->__("Portofoliu foto-video")
		));

		$this->renderLayout();
	}

	// private function _checkForInvalidInput($postParams)
	// {
		// //Zend_Debug::dump($postParams); die();
		// foreach($postParams as $key => $val){
			// if(in_array($key, $this->_registeredParams)){
				// $postParams[$key] = $val;
			// }else{
				// unset($key);
			// }
		// }
		// return $postParams;
	// }

	/*
	* Album Actions
	*/

	public function albumAction()
	{
	    // redirect customer if he is not from furnizori group
        if ($this->customerSession()->getCustomer()->getGroupId() != '4') {
            $this->_redirect('customer/account/');
            return ;
        }

		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->getLayout()->getBlock("head")->setTitle($this->__("Editare albume foto"));

		$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
		$breadcrumbs->addCrumb("home", array(
			"label" => $this->__("Home Page"),
			"title" => $this->__("Home Page"),
			"link"  => Mage::getBaseUrl()
		));

		$breadcrumbs->addCrumb("editare albume foto", array(
			"label" => $this->__("Editare albume foto"),
			"title" => $this->__("Editare albume foto")
		));

		$this->renderLayout();
	}

	public function savealbumAction()
    {
        // get secret key and post params
        $secretKey = Mage::getSingleton('core/session')->getFormKey();
        $paramsArr = $this->getRequest()->getParams();
        $albumId = $paramsArr['album_id'] != '' ? $paramsArr['album_id'] : 'new';
        //echo $albumId; die();
        $customerId = $this->customerSession()->getCustomer()->getId();

        // delete tags and unregistered params
        foreach($paramsArr as $k => $v){
            if(in_array($k, $this->_registeredParams)) {
                if (!is_array($v)) $paramsArr[$k] = trim($this->stripTags($v));
            } else {
                unset($paramsArr[$k]);
            }
        }
        
        //echo $albumId; die();
        //Zend_Debug::dump($paramsArr); die();

        if(!$this->getRequest()->isPost('form_key') && $paramsArr['form_key'] != $secretKey){ // check if secret key is matching
            $this->customerSession()->addError('S-a ivit o eroare la salvarea albumului. Te rugăm să încerci mai târziu.');
            return $this->_redirect('customer/account/');
        } elseif (!$this->_isAlbumOwner($albumId)) { // check album owner
            //die($albumId);
            $this->customerSession()->addError('Nu ai permisiunea să editezi acest album.');
            return $this->_redirect('customer/account/');
        } elseif (empty($paramsArr['album_name']) || empty($paramsArr['album_description'])) {
            //Zend_Debug::dump($paramsArr); die();
            $this->customerSession()->addError($this->__('Campurile marcate cu steluta sunt obligatorii.'));
            $url = $albumId != 'new' ? 'portofoliu/edit/album/id/' . $albumId : 'portofoliu/edit/album/';
            return $this->_redirect($url);
        } else {
            // everything seems to be ok, we can start saving the album details to db
            //Zend_Debug::dump($paramsArr); die();
            /*
            $paramsArr (new) = array(8) {
              ["album_id"] => string(0) ""
              ["cover_img"] => string(0) ""
              ["form_key"] => string(16) "LNkFrQxfL97dDehy"
              ["album_name"] => string(43) "Album foto Irina si Marcel (25 august 2014)"
              ["is_visible"] => string(1) "1"
              ["album_description"] => string(277) "Fusce eu vestibulum lectus, ac congue lacus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas egestas vehicula felis in consequat. Curabitur ut justo iaculis ante lacinia blandit eu id sapien. Donec tincidunt vulputate tincidunt."
              ["new_thumb_img"] => array(3) {
                [0] => string(30) "1423593809-thumb-wedding12.jpg"
                [1] => string(30) "1423593823-thumb-wedding11.jpg"
                [2] => string(34) "1423593830-thumb-wedding-photo.jpg"
              }
              ["new_full_img"] => array(3) {
                [0] => string(24) "1423593809-wedding12.jpg"
                [1] => string(24) "1423593823-wedding11.jpg"
                [2] => string(28) "1423593830-wedding-photo.jpg"
              }
            }

            $paramsArr (existing) array(7) {
              ["album_id"] => string(2) "23"
              ["cover_img"] => string(24) "1423593809-wedding12.jpg"
              ["form_key"] => string(16) "LNkFrQxfL97dDehy"
              ["album_name"] => string(43) "Album foto Irina si Marcel (25 august 2014)"
              ["is_visible"] => string(1) "1"
              ["album_description"] => string(277) "Fusce eu vestibulum lectus, ac congue lacus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas egestas vehicula felis in consequat. Curabitur ut justo iaculis ante lacinia blandit eu id sapien. Donec tincidunt vulputate tincidunt."
              ["photo_order"] => array(5) {
                [99] => string(1) "2"
                [96] => string(1) "1"
                [97] => string(1) "3"
                [95] => string(1) "4"
                [98] => string(1) "4"
              }
            }

            */
            $album = $this->albumModel();

            if ($albumId != 'new') { // update existing album
                $album->load($albumId);
            }

            if ($albumId == 'new') {
                $album->setCustomerId($customerId);
                $album->setAlbumCover($paramsArr['new_full_img'][0]);
            }

            $album->setAlbumName($this->stripTags($paramsArr['album_name']));
            $album->setIsVisible($paramsArr['is_visible']);
            $album->setAlbumDescription($paramsArr['album_description']);
            $album->setCreatedAt(gmdate('Y-m-d H:i:s', time()));
            $album->save();
            
            //Zend_Debug::dump($album->getData()); die();

            // get max allowed active albums and act accordingly
            $maxActiveAlbums = $this->customerSession()->getCustomer()->getAcOpMaxAlbumActive();

            if ($maxActiveAlbums < Mage::helper('portofoliu')->countAlbums($customerId, true) && $album->getIsVisible()) {
                $this->customerSession()->addNotice($this->__('Configurația contului tău iți permite să ai maxim %s', $this->customerSession()->getCustomer()->getAcOpMaxAlbumActive() . ' albume active.'));
                $album->setIsVisible(0)->save();
            }

            /**
             * continue with album's photos
             */
            $tmpDirectory = Mage::getBaseDir() . DS . 'media' . DS . 'tmp_uploads' . DS . $customerId . DS;
            $finalDirectory = Mage::getBaseDir() . DS . 'media' . DS . 'albums' . DS;

            // delete selected photos
            if (isset($paramsArr['delete_existing'])) {
                foreach ($paramsArr['delete_existing'] as $id) {
                    $photo = Mage::getModel('portofoliu/photos')->load($id);
                    if ($photo->getId()) {
                        if ($photo->getSortOrder() == 1) {
                            $album->setAlbumCover(NULL)->save();
                        }
                        $photo->delete();
                        // remove photos from server
                        if (file_exists($finalDirectory . $customerId . DS . $photo->getThumbUrl())) {
                            unlink($finalDirectory . $customerId . DS . $photo->getThumbUrl());
                        }
                        if (file_exists($finalDirectory . $customerId . DS . $photo->getFullImgUrl())) {
                            unlink($finalDirectory . $customerId . DS . $photo->getFullImgUrl());
                        }
                    }
                }
            }

            if ($albumId == 'new') {
                $photosCount = count($paramsArr['new_full_img']);
            } else {
                $photosCount = Mage::helper('portofoliu')->countPhoto($album->getId());
            }

            //Zend_Debug::dump($photosCount); die();

            $saveCover = false;
            // if ablum has photos and it is not new (re)save their sort order
            if ($photosCount && $albumId != 'new') {
                // load photos
                $photos = Mage::getModel('portofoliu/photos')->getCollection()->addFieldToFilter('album_id', $album->getId());

                foreach ($photos as $photo) {
                    $photo->setSortOrder($paramsArr['photo_order'][$photo->getId()]);

                    if ($paramsArr['photo_order'][$photo->getId()] == 1) {
                        $album->setAlbumCover($photo->getFullImgUrl());
                        $album->save();
                    }
                }

                $photos->save();

            } elseif ($photosCount == 0) {
                $saveCover = true;
            }

            $stopped = false;
            // save new uploaded photos
            if (isset($paramsArr['new_thumb_img'])) {
                //die('in');
                // the album has new images
                for ($i = 0; $i < count($paramsArr['new_thumb_img']); $i++) {
                    $photosCount++;
                    $photos = Mage::getModel('portofoliu/photos');
                    $photos->setAlbumId($album->getId())
                           ->setPhotoLabel($paramsArr['album_name'])
                           ->setThumbUrl($paramsArr['new_thumb_img'][$i])
                           ->setFullImgUrl($paramsArr['new_full_img'][$i]);

                    //Zend_Debug::dump($photos->getData()); die();

                    if ($albumId == 'new') {
                        $photos->setSortOrder($i + 1);
                    } else {
                        $photos->setSortOrder($photosCount);

                        if ($saveCover && $photosCount == 1) {
                            $album->setAlbumCover($paramsArr['new_full_img'][$i]);
                            $album->save();
                        }
                    }
                    
                    //Zend_Debug::dump($photos->getData()); die();

                    // check if maxim allocated space was exceeded
                    if (Mage::helper('portofoliu')->maxAllocatedSpaceExceeded($finalDirectory . $customerId . DS)) {
                        //Zend_Debug::dump(Mage::helper('portofoliu')->maxAllocatedSpaceExceeded($finalDirectory)); die('size');
                        //die('stopped');
                        $stopped = true;
                        break;
                    } else {
                        //die('continue');
                        $photos->save();

                        $this->_moveUploadedFile($tmpDirectory, $finalDirectory, $paramsArr['new_thumb_img'][$i]);
                        $this->_moveUploadedFile($tmpDirectory, $finalDirectory, $paramsArr['new_full_img'][$i]);
                        // echo $tmpDirectory .''.$finalDirectory.''.$paramsArr['new_thumb_img'][$i].'<br />';
                        // echo $tmpDirectory .''.$finalDirectory.''.$paramsArr['new_full_img'][$i].'<br />';
                        // die();
                    }
                    //die('end');
                }

            }

            // remove temporary upload directory
            $this->removeNonEmptyDir($tmpDirectory);

            //Zend_Debug::dump($album->getData()); die('saved');
            if (!$album->getAlbumCover() || !file_exists($finalDirectory . $customerId . DS . $album->getAlbumCover())) {
                $album->delete();
                // $this->customerSession()->addError($this->__('Ceva neasteptat s-a intamplat. Te rugam sa incerci mai tarziu. Daca situati persista nu ezita sa ne contactezi.'));
                $this->customerSession()->addError($this->__('Ai ajuns la limita maximă de stocare alocată contului tău. Albumul nu a fost salvat'));
                return $this->_redirect('customer/account/');
            }

            //Zend_Debug::dump($paramsArr);
            if (!$stopped) {
                $this->customerSession()->addSuccess($this->__('Albumul "<strong>%s', $paramsArr['album_name'] . '</strong>" a fost salvat.'));
            } else {
                $this->customerSession()->addNotice($this->__('Albumul "<strong>%s', $paramsArr['album_name'] . '</strong>" a fost salvat dar o parte din imagini nu au fost uploadate deoarece ai ajuns la limita maximă de stocare alocată contului tău.'));
            }

            return $this->_redirect('portofoliu/edit/album/id/' . $album->getId());

        }

        return;
    }

    protected function _moveUploadedFile($tmpDirectory, $finalDirectory, $image)
    {
        //echo $tmpDirectory .''.$finalDirectory.''.$image; die();
        $customerId = $this->customerSession()->getCustomer()->getId();

        if (file_exists($tmpDirectory . $image)) {

            if (!file_exists($finalDirectory)) {
                mkdir($finalDirectory);
            }

            $finalDirectory .= $customerId . DS;

            if (!file_exists($finalDirectory)) {
                mkdir($finalDirectory);
            }

            rename($tmpDirectory . $image, $finalDirectory . $image);

        }

        return false;
    }

    public function removeNonEmptyDir($dir)
    {
       if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
           if ($object != "." && $object != "..") {
             if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
           }
         }
         reset($objects);
         rmdir($dir);
       }
     }

	function getFriendlyUrl($string, $ext = false)
    {
	    $string = str_replace(array('[\', \']'), '', $string);
	    $string = preg_replace('/\[*\]/U', '', $string);
	    $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
	    $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
	    $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);

		if($ext){
			return strtolower(trim($string, '-')).$ext;
		}else{
			return strtolower(trim($string, '-'));
		}
	}

	public function deletealbumAction()
	{
		$albumId = $this->getRequest()->getParam('id');

		 // check if the customer is the album's owner
		if($this->_isAlbumOwner($albumId)){
			// load album model and delete given album
			$albumModel = Mage::getModel('portofoliu/albums');
			$albumModel->setId($albumId)->delete();

			// get photos and delete them
			$photosModel = Mage::getModel('portofoliu/photos');
			$photosCollection = $photosModel->getCollection()->addFieldToFilter('album_id', $albumId);

			foreach($photosCollection as $photo){
				// delete photo fron data base
				$photosModel->setId($photo->getPhotoId())->delete();
				// delete photos (full size and thumb) from directory
				$phtoPath = Mage::getBaseDir() . DS . 'media' . DS . 'albums' . DS . $this->customerSession()->getCustomerId() . DS . $photo->getFullImgUrl();
				if(file_exists($phtoPath)) unlink($phtoPath);

				$thumbPhtoPath = Mage::getBaseDir() . DS . 'media' . DS . 'albums' . DS . $this->customerSession()->getCustomerId() . DS . $photo->getThumbUrl();
				if(file_exists($thumbPhtoPath)) unlink($thumbPhtoPath);
			}
			// set success message and redirec to furnizor account
			$this->customerSession()->addSuccess('Albumul a fost sters.');
			return $this->_redirect('customer/account/');
		}else{
			// set error message and redirec to furnizor account
			$this->customerSession()->addError('Nu ai permisiunea sa editezi acest album.');
			return $this->_redirect('customer/account/');
		}

		return;
	}

	private function deleteDirectory($dir) {
	    if (!file_exists($dir)) return true;
	    if (!is_dir($dir)) return unlink($dir);
	    foreach (scandir($dir) as $item) {
	        if ($item == '.' || $item == '..') continue;
	        if (!$this->deleteDirectory($dir.DS.$item)) return false;
	    }
	    return rmdir($dir);
	}

	private function _isAlbumOwner($albumId)
	{
		if($albumId != 'new') { // edit existing
			$albumDetails = $this->albumModel()->load($albumId);
            //die($this->customerSession()->getCustomerId());
			if($this->customerSession()->getCustomerId() == $albumDetails->getCustomerId()){
				return true;
			}
		} elseif ($albumId == 'new') {
		    // add new album
            return true;
		}

        return false;
	}

	/*
	* Video Actions
	*/

	public function videoAction()
	{
	    // redirect customer if is not from furnizori group
        if ($this->customerSession()->getCustomer()->getGroupId() != '4') {
            $this->_redirect('customer/account/');
            return ;
        }

		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
		$this->getLayout()->getBlock("head")->setTitle($this->__("Editare fisiere video"));

		$breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
		$breadcrumbs->addCrumb("home", array(
			"label" => $this->__("Home Page"),
			"title" => $this->__("Home Page"),
			"link"  => Mage::getBaseUrl()
		));

		$breadcrumbs->addCrumb("editare fisiere video", array(
			"label" => $this->__("Editare fisiere video"),
			"title" => $this->__("Editare fisiere video")
		));

		$this->renderLayout();
	}

	public function savevideoAction()
	{
	    // redirect customer if is not from furnizori group
        if ($this->customerSession()->getCustomer()->getGroupId() != '4') {
            $this->_redirect('customer/account/');
            return ;
        }

		// get secret key and post params
		$secretKey = Mage::getSingleton('core/session')->getFormKey();
		$paramsArr = $this->getRequest()->getParams();
        //Zend_Debug::dump($paramsArr);
        $videoId = $paramsArr['video_id'] != '' ? $paramsArr['video_id'] : 'new';
        //echo $videoId;
		//Zend_Debug::dump($paramsArr); die();

		// delete tags and unregistered params
        foreach($paramsArr as $k => $v){
            if(in_array($k, $this->_registeredParams)) {
                if (strpos($v, 'iframe') !== true) { // it is youtube iframe
                    if (!is_array($v)) $paramsArr[$k] = trim($v);
                } else {
                    if (!is_array($v)) $paramsArr[$k] = trim($this->stripTags($v));
                }
            } else {
                unset($paramsArr[$k]);
            }
        }

		if(!$this->getRequest()->isPost('form_key') && $paramsArr['form_key'] != $secretKey){ // check if secret key is matching
            $this->customerSession()->addError('S-a ivit o eroare la salvarea videoclipului. Te rugam sa incerci mai tarziu.');
            return $this->_redirect('customer/account/', array('_secure' => true));
        } elseif (!$this->_isVideoOwner($videoId)) { // check video owner
            //echo 'is not owner<br />';
            $this->customerSession()->addError('Nu ai permisiunea sa editezi acest videoclip.');
            return $this->_redirect('customer/account/', array('_secure' => true));
        } else {
            //echo 'is owner<br />';
            //die($videoId);
            // everything seems to be ok, we can start saving the video details to db
            $video = $this->videoModel();

            if ($videoId != 'new') { // update existing video
                $video->load($videoId);
            }

            $videoUrl = $paramsArr['video_url'];
            $videoIdFromUrl = $this->getVideoIdFromUrl($videoUrl);
            $videoType = $this->_videoType;

            //echo $videoUrl . $videoIdFromUrl . $videoType;die();

            $video->setVideoName($paramsArr['video_name'])
                   ->setVideoDescription($paramsArr['video_description'])
                   ->setVideoUrl($videoIdFromUrl)
                   ->setUrlType($videoType)
                   ->setIsVisible($paramsArr['is_visible']);

            if ($videoId == 'new') {
                $video->setCustomerId($this->customerSession()->getCustomer()->getId());
            }

            $video->save();

            // get max allowed active albums and act accordingly
            $maxActiveVideos = $this->customerSession()->getCustomer()->getAcOpMaxVideoActive();
            if ($maxActiveVideos < Mage::helper('portofoliu')->countVideos($this->customerSession()->getCustomer()->getId(), true) && $video->getIsVisible()) {
                $this->customerSession()->addNotice($this->__('Configuratia contului tau iti permite sa ai maxim %s', $this->customerSession()->getCustomer()->getAcOpMaxVideoActive() . ' videoclipuri active.'));
                $video->setIsVisible(0)->save();
            }

            // Zend_Debug::dump($paramsArr); die();
            $this->customerSession()->addSuccess($this->__('Videoclipul "%s', $paramsArr['video_name'] . '" a fost salvat.'));
            return $this->_redirect('portofoliu/edit/video/id/' . $video->getId());
        }

        return;
    }

                /*
				if($this->getRequest()->getParam('action') == 'existing'){
					$videoUrl = Mage::app()->getRequest()->getParam('video_url');
					$videoIdFromUrl = $this->getVideoIdFromUrl($videoUrl);

			        if(isset($videoIdFromUrl)){

						$existingVideo = $this->videoModel()->load($paramsArr['video_id']);
						//Zend_Debug::dump($existingVideo->getData()); die();

						$existingVideo->setVideoName($paramsArr['video_name'])
								   	   ->setVideoDescription($paramsArr['video_description'])
									   ->setVideoUrl($videoIdFromUrl)
									   ->setUrlType($this->getVideoTypeFromUrl($videoUrl))
								       ->setIsVisible($paramsArr['is_visible']);
						$existingVideo->save();

						$activeVideos = Mage::helper('portofoliu')->getPublicVideos($this->customerSession()->getCustomerId());
						$tipContFurnizor = Mage::getModel('furnizori/collection')->getFurnizorDetails($this->customerSession()->getCustomerId())->getTipContFurnizor();

						switch($tipContFurnizor){
							case '108': // free
								$planId = '117';
								$plan = Mage::getModel('catalog/product')->load($planId);
								break;
							case '91': // basic
								$planId = '118';
								$plan = Mage::getModel('catalog/product')->load($planId);
								break;
							case '90': // premium
								$planId = '119';
								$plan = Mage::getModel('catalog/product')->load($planId);
								break;
						}

						$allowedActiveVideos = $plan->getResource()->getAttribute('pl_max_video_active')->getFrontend()->getValue($plan);

						if(count($activeVideos) > $allowedActiveVideos){
							// set all videos as privat excluding this one
							foreach($activeVideos as $video){
								$video->setIsVisible(0);
							}
							$activeVideos->save();
							// re-activate this video
							$existingVideo->setIsVisible(1)->save();
							// set error message
							$this->customerSession()->addError('Configuratia contului tau iti permite sa ai cel mult '.$allowedActiveVideos.' videoclip(uri) public(e).');
						}

			            // set succes message and redirect to fornizor dashboard
						$this->customerSession()->addSuccess('Videoclipul "'.$paramsArr['video_name'].'" a fost salvat.');
						return $this->_redirect('customer/account/', array('_secure' => true));

			        }else{
			        	$this->customerSession()->addError('Linkul videoclipului nu a fost corect. Videoclipul nu a fost salvat.');
						return $this->_redirect('portofoliu/edit/video/id/' . $paramsArr['video_id'], array('_secure' => true));
			        }
		        }elseif($this->getRequest()->getParam('action') == 'new' && $this->_isAlbumOwner($paramsArr['video_id']) == 'new'){
		        	// die('save new video');
					$videoUrl = Mage::app()->getRequest()->getParam('video_url');
					$videoIdFromUrl = $this->getVideoIdFromUrl($videoUrl);

					if(isset($videoIdFromUrl)){

						$newVideo = $this->videoModel();
						//Zend_Debug::dump($newVideo->getData()); die();

						$newVideo->setCustomerId($this->customerSession()->getCustomerId())
							     ->setVideoName($paramsArr['video_name'])
						   	     ->setVideoDescription($paramsArr['video_description'])
							     ->setVideoUrl($videoIdFromUrl)
							     ->setUrlType($this->getVideoTypeFromUrl($videoUrl))
						         ->setIsVisible($paramsArr['is_visible']);
						$newVideo->save();

						$activeVideos = Mage::helper('portofoliu')->getPublicVideos($this->customerSession()->getCustomerId());
						$tipContFurnizor = Mage::getModel('furnizori/collection')->getFurnizorDetails($this->customerSession()->getCustomerId())->getTipContFurnizor();

						switch($tipContFurnizor){
							case '108': // free
								$planId = '117';
								$plan = Mage::getModel('catalog/product')->load($planId);
								break;
							case '91': // basic
								$planId = '118';
								$plan = Mage::getModel('catalog/product')->load($planId);
								break;
							case '90': // premium
								$planId = '119';
								$plan = Mage::getModel('catalog/product')->load($planId);
								break;
						}

						$allowedActiveVideos = $plan->getResource()->getAttribute('pl_max_video_active')->getFrontend()->getValue($plan);

						if(count($activeVideos) > $allowedActiveVideos){
							// set all videos as privat excluding this one
							foreach($activeVideos as $video){
								$video->setIsVisible(0);
							}
							$activeVideos->save();
							// re-activate this video
							$newVideo->setIsVisible(1)->save();
							// set error message
							$this->customerSession()->addError('Contul tau iti permite sa ai cel mult '.$allowedActiveVideos.' videoclip(uri) public(e).');
						}

			            // set succes message and redirect to fornizor dashboard
						$this->customerSession()->addSuccess('Videoclipul "'.$paramsArr['video_name'].'" a fost salvat.');
						return $this->_redirect('customer/account/', array('_secure' => true));
			        }else{
			        	// set error message and redirect to fornizor dashboard
			        	$this->customerSession()->addError('Linkul videoclipului nu a fost corect. Videoclipul nu a fost salvat.');
						return $this->_redirect('customer/account/', array('_secure' => true));
			        }
		        }
			}
		}else{
			$this->customerSession()->addError('S-a ivit o eroare la salvarea videoclipului. Te rugam sa incerci mai tarziu.');
			return $this->_redirect('customer/account/', array('_secure' => true));
		}
		return;
	}

    */

	private function getVideoIdFromUrl($videoUrl)
	{
		$videoIdformUrl = false;
		// get video id from youtube url / embed
		if(strpos($videoUrl, 'iframe') !== false && strpos($videoUrl, 'youtube') !== false){ // is embed code
		    //die('y embed'); // <iframe width="560" height="315" src="//www.youtube.com/embed/kyibyraGBnc" frameborder="0" allowfullscreen></iframe>
            $this->_videoType = 'youtube';
            $videoUrlArr = explode("embed/", $videoUrl);
            $videoUrlArr = explode('"', $videoUrlArr[1]);
            $videoIdformUrl = $videoUrlArr[0];
			return $videoIdformUrl;
        }elseif(strpos($videoUrl, 'youtu.be') !== false){ // is share link from yotube
            //die('d link'); // http://youtu.be/JStoQ5sh5vM
            $this->_videoType = 'youtube';
            $videoUrlArr = explode("/", $videoUrl);
            $videoIdformUrl = end($videoUrlArr);
			return $videoIdformUrl;
        }elseif(strpos($videoUrl, 'youtube.com') !== false && strpos($videoUrl, 'watch?') !== false) { // is url from browser
        	//die('y broser'); // http://www.youtube.com/watch?v=JStoQ5sh5vM
        	$this->_videoType = 'youtube';
            $videoUrlArr = explode("=", $videoUrl);
            $videoIdformUrl = end($videoUrlArr);
            return $videoIdformUrl;
        }

		// get video id from vimeo
		if(strpos($videoUrl, '//player.vimeo.com/video/')){ // is embed code
			//die('v embed'); // <iframe src="//player.vimeo.com/video/105686970" width="500" height="213" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe> <p><a href="http://vimeo.com/105686970">Who We Are</a> from <a href="http://vimeo.com/sicmanta">Sicmanta</a> on <a href="https://vimeo.com">Vimeo</a>.</p>
            $this->_videoType = 'vimeo';
            $videoUrlArr = explode("video/", $videoUrl);
            $videoUrlArr = explode('"', $videoUrlArr[1]);
            $videoIdformUrl = $videoUrlArr[0];
			return $videoIdformUrl;
        //}elseif(strpos($videoUrl, 'vimeo.com/') !== false && strpos($videoUrl, 'channels') !== false){ // is url from browser
        }elseif(strpos($videoUrl, 'vimeo.com/') !== false){ // is url from browser
        	//die('v browser'); // http://vimeo.com/channels/staffpicks/105686970
            $this->_videoType = 'vimeo';
            $videoUrlArr = explode("/", $videoUrl);
            $videoIdformUrl = end($videoUrlArr);
            return $videoIdformUrl;
        }else{ // given url is wrong
            //die('wrong');
        	return false;
        }

		return false;
	}

	// public function getVideoTypeFromUrl($videoUrl)
	// {
		// if(strpos($videoUrl, 'embed') || strpos($videoUrl, 'youtube') || strpos($videoUrl, 'youtu.be')){
			// return 'youtube';
		// }elseif(strpos($videoUrl, 'vimeo')){
			// return 'vimeo';
		// }else{
			// return false;
		// }
	// }

	public function deletevideoAction()
	{
		$videoId = $this->getRequest()->getParam('id');

		 // check if the customer is the video's owner
		if($this->_isVideoOwner($videoId)){
			// load video model and delete given video
			$videoModel = Mage::getModel('portofoliu/videos');
			$videoModel->setId($videoId)->delete();

			$this->customerSession()->addSuccess('Videoclipul a fost sters.');
			return $this->_redirect('customer/account/', array('_secure' => true));
		}else{
			// set error message and redirec to furnizor account
			$this->customerSessionr()->addError('Nu ai permisiunea sa editezi acest videoclip.');
			return $this->_redirect('customer/account/', array('_secure' => true));
		}

		return;
	}

    private function _isVideoOwner($videoId)
    {
        if($videoId != 'new') { // edit existing
            $videoDetails = $this->videoModel()->load($videoId);
            //die($this->customerSession()->getCustomerId());
            if($this->customerSession()->getCustomerId() == $videoDetails->getCustomerId()){
                //echo 'existing<br />';
                return true;
            }
        } elseif ($videoId == 'new') {
            // add new album
            //echo 'is new<br />';
            return true;
        }

        return false;
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


}

















