<?php
class Picode_Portofoliu_Helper_Data extends Mage_Core_Helper_Abstract
{
	private $_customerId = null;
	private $_albumCollection;
	private $_videoCollection;
	private $_photoCollection;
	
	public function __construct()
	{
		$this->_customerId      = Mage::getSingleton('customer/session')->getCustomer()->getId();
		$this->_albumCollection = Mage::getModel('portofoliu/albums')->getCollection();
		$this->_videoCollection = Mage::getModel('portofoliu/videos')->getCollection();
		$this->_photoCollection = Mage::getModel('portofoliu/photos')->getCollection();
	}
	
	public function countAlbums($customerId = false, $isVisible = false)
	{
		if($customerId) $this->_customerId = $customerId;
		
		$this->_albumCollection->addFieldToFilter('customer_id', $this->_customerId);
		if($isVisible) $this->_albumCollection->addFieldToFilter('is_visible', true);
		
		return $this->_albumCollection->count();
	}
	
	public function getPublicAlbums($customerId)
	{
		$albumCollection = $this->_albumCollection;
		$albumCollection
				->addFieldToFilter('customer_id', $customerId)
				->addfieldToFilter('is_visible', 1)
				->setOrder('created_at', 'DESC');
		return $albumCollection;
	}
	
	public function countVideos($customerId = false, $isVisible = false)
	{
		if($customerId) $this->_customerId = $customerId;
		
		$this->_videoCollection->addFieldToFilter('customer_id', $this->_customerId);
		if($isVisible) $this->_videoCollection->addFieldToFilter('is_visible', true);
		
		return $this->_videoCollection->count();
	}
	
	public function getPublicVideos($customerId)
	{
		$videoCollection = $this->_videoCollection;
		$videoCollection
				->addFieldToFilter('customer_id', $customerId)
				->addfieldToFilter('is_visible', 1)
				->setOrder('created_at', 'DESC');
		return $videoCollection;
	}
	
	public function countPhoto($albumId)
	{
		$photoColection = $this->_photoCollection->addFieldToFilter('album_id', $albumId);
		return $photoColection->count();
	}
	
	public function getUnicVisit($portofoliuType, $portofoliuId)
	{
		
	}
	
	public function getAlbums()
	{
		return $this->_albumCollection;
	}
	
	public function getUserIpAddress()
	{
	    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	    	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else
	    	$ip = $_SERVER['REMOTE_ADDR']; 
	    
	    $is_local_network = false;
	    
	    if(
	        (strpos($ip,'192.168') === 0)|| 
	        (strpos($ip,'10.') === 0)    || 
	        (strpos($ip,'172.16') === 0) || 
	        (strpos($ip,'172.31') === 0) || 
	        (strpos($ip,'172.0.0.1') === 0)
		   )
	    {
	        $is_local_network = true;
	    }
	    
	    if($is_local_network){
		    $ips = dns_get_record($_SERVER['SERVER_NAME']);
	        foreach ($ips as $item) {
	            if(isset($item['type']))
	            	if($item['type']=='A')
	            		if(isset($item['ip'])){
	            			//die('ext '.$item['ip']);
			                return $item['ip'];
			                break;
			            }
	        }
	    }else{
	    	//die('ip '.$ip);
	    	return $ip;
	    }
	}
	
	public function resizeImg($img, $width, $height = false, $customerId = false)
	{
		if(!isset($customerId)) $customerId = $this->_customerId;
		
		$_media_dir = Mage::getBaseDir('media') . DS . 'albums' . DS . $customerId . DS;
		$imgSize = getimagesize(Mage::getBaseDir('media') . DS . 'albums' . DS . $customerId . DS . $img);
		// real image sizes
		$imgWidth = $imgSize[0];
		$imgHeight = $imgSize[1];
		
		if($imgWidth > $imgHeight){
			$imgProp = $imgWidth / $imgHeight;
			$newWidth = $width;
			$newHeight = $width / $imgProp;
		}elseif($imgWidth < $imgHeight){
			$imgProp = $imgHeight / $imgWidth;
			$newWidth = $width / $imgProp;
			$newHeight = $width;
		}elseif($imgWidth == $imgHeight){
			$newWidth = $newHeight = $width;
		}
		
	    $cache_dir = $_media_dir . 'cache' . DS . $width . DS;
	             
	    if(file_exists($_media_dir . $img)){
	    	
			if(!is_dir($_media_dir . 'cache' . DS)){
				mkdir($_media_dir . 'cache');
			}elseif(!is_dir($cache_dir)){
				mkdir($cache_dir);
			}
		        
	        $_image = new Varien_Image($_media_dir . $img);
	        $_image->constrainOnly(FALSE);
            $_image->keepAspectRatio(TRUE);
			$_image->keepFrame(TRUE);
			$_image->keepTransparency(TRUE);
			$_image->backgroundColor(array(255,255,255));
			$_image->setImageBackgroundColor(TRUE);
			$_image->quality(100);
	        //$_image->resize($width, $height);
	        $_image->resize($newWidth,$newHeight);
	        $_image->save($cache_dir . $img);
		
	        return Mage::getBaseUrl() . 'media/albums/' . $customerId . '/cache/' . $width . '/' . $img;
	    }
	     return false;
	}

	public function getFurnizorLink($furnizorId)
	{
		$product = Mage::getModel('catalog/product')->loadByAttribute('id_furnizor', $furnizorId);
		return $product->getUrlKey();
	}

	public function getFurnizorLogo($furnizorId)
	{
		$product = Mage::getModel('catalog/product')->loadByAttribute('id_furnizor', $furnizorId);
		$logoUrl = Mage::getModel('catalog/product_media_config')->getMediaUrl($product->getThumbnail());
		$logoUrlArr = explode('/', $logoUrl);
		
		if(end($logoUrlArr) != 'no_selection'){
			return $logoUrl;
		}else{
			return  Mage::getDesign()->getSkinUrl('images/no-logo.png');
		}
	}
    
    public function maxAllocatedSpaceExceeded($finalDirectory)
    {
        $maxAllocatedSize = Mage::getSingleton('customer/session')->getCustomer()->getAcOpSpatiuDisc() * 1048576; // size in MB
        $occupiedSize = $this->recursiveDirectorySize($finalDirectory);
        
        // Zend_Debug::dump($finalDirectory);
        // Zend_Debug::dump($maxAllocatedSize); // int(52428800)
        // Zend_Debug::dump($occupiedSize);     // int(-1)
        // die();
        
        if ($occupiedSize >= $maxAllocatedSize) {
            return true;
        }
        
        return false;
    }
	
    // ------------ lixlpixel recursive PHP functions -------------
    // recursive_directory_size( directory, human readable format )
    // expects path to directory and optional TRUE / FALSE
    // PHP has to have the rights to read the directory you specify
    // and all files and folders inside the directory to count size
    // if you choose to get human readable format,
    // the function returns the filesize in bytes, KB and MB
    // ------------------------------------------------------------
    
    // to use this function to get the filesize in bytes, write:
    // recursive_directory_size('path/to/directory/to/count');
    
    // to use this function to get the size in a nice format, write:
    // recursive_directory_size('path/to/directory/to/count',TRUE);
    
    public function recursiveDirectorySize($directory, $format = FALSE)
    {
        $size = 0;
    
        // if the path has a slash at the end we remove it here
        if(substr($directory,-1) == '/')
        {
            $directory = substr($directory,0,-1);
        }
    
        // if the path is not valid or is not a directory ...
        if(!file_exists($directory) || !is_dir($directory) || !is_readable($directory))
        {
            // ... we return -1 and exit the function
            return -1;
        }
        // we open the directory
        if($handle = opendir($directory))
        {
            // and scan through the items inside
            while(($file = readdir($handle)) !== false)
            {
                // we build the new path
                $path = $directory.'/'.$file;
    
                // if the filepointer is not the current directory
                // or the parent directory
                if($file != '.' && $file != '..')
                {
                    // if the new path is a file
                    if(is_file($path))
                    {
                        // we add the filesize to the total size
                        $size += filesize($path);
    
                    // if the new path is a directory
                    }elseif(is_dir($path))
                    {
                        // we call this function with the new path
                        $handlesize = $this->recursiveDirectorySize($path);
    
                        // if the function returns more than zero
                        if($handlesize >= 0)
                        {
                            // we add the result to the total size
                            $size += $handlesize;
    
                        // else we return -1 and exit the function
                        }else{
                            return -1;
                        }
                    }
                }
            }
            // close the directory
            closedir($handle);
        }
        // if the format is set to human readable
        if ($format == TRUE) {
            // if the total size is bigger than 1 MB
            if ($size / 1048576 > 1) {
                return round($size / 1048576, 1) . ' MB';
    
            // if the total size is bigger than 1 KB
            } elseif ($size / 1024 > 1) {
                return round($size / 1024, 1) . ' KB';
    
            // else return the filesize in bytes
            } else {
                return round($size, 1) . ' bytes';
            }
        } else {
            // return the total filesize in bytes
            return $size;
        }
    }

    public function getProviderDetails($providerId = false)
    {
        if (!$providerId) {
            $providerId = $this->_customerId;
        }

        return Mage::getModel('customer/customer')->load($providerId);
    }

	public function checkVideoExists($videoId, $videoType)
	{
	    //echo $videoId; echo $videoType; die();
		$headers = array();
        //echo $videoId; echo $videoType; die(); 

		if ($videoType == 'youtube') {
			$headers = get_headers('http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=' . $videoId . '&format=json');
		} elseif($videoType == 'vimeo') {
		    //echo $videoId;die();
			$headers = get_headers('https://vimeo.com/' . $videoId);
		}

		if (!isset($headers[0])) {
			return false;
		}

		if (strpos(strtolower($headers[0]), '200') || strpos(strtolower($headers[0]), 'ok')) {
			return true;
		}

		return false;
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
	
}
	 