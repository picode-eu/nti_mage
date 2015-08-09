<?php

class Picode_Popularity_Helper_Data extends Mage_Core_Helper_Abstract
{
    //private $url;
	private $timeout;
	
	//function __construct($url, $timeout = 10)
	function __construct($timeout = 10)
	{
		//$this->url = rawurlencode($url);
		$this->timeout = $timeout;
	}
    
    /*
     * get user's ip address
     */
    public function getCustomerIpAddress()
    {
         if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
             $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
         } else {
             $ip = $_SERVER['REMOTE_ADDR'];
         }
        
         $is_local_network = false;
        
         if (
              (strpos($ip,'192.168') === 0)||
              (strpos($ip,'10.') === 0)      ||
              (strpos($ip,'172.16') === 0) ||
              (strpos($ip,'172.31') === 0) ||
              (strpos($ip,'172.0.0.1') === 0)
            )
         {
              $is_local_network = true;
         }
        
         if ($is_local_network) {
              $ips = dns_get_record($_SERVER['SERVER_NAME']);
             
              foreach ($ips as $item) {
                   if(isset($item['type']))
                        if($item['type']=='A')
                             if(isset($item['ip'])){
                                  return $item['ip'];
                                  break;
                             }
              }

         } else {
              return $ip;
         }
         
         return null;
    }

    public function botDetected()
    {
        $ip = $this->getCustomerIpAddress();
        $blockedIps = array(); 
        //$blockedIps = array('23.253.162.123', '74.112.131.244', '23.96.208.137', '150.70.97.122', '64.13.133.149', '64.13.133.152', '66.249.92.8', '216.46.175.35', '216.46.190.190', '74.112.131.242', '150.70.173.43', '74.112.131.242', '66.249.83.60', '66.249.88.205', '66.102.6.98');
        
        if (in_array($ip, $blockedIps)) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    
    }

	function getTweets($url)
	{
		$json_string = $this->fileGetContentsCurl('http://urls.api.twitter.com/1/urls/count.json?url=' . $this->rawUrlEncode($url));
		$json = json_decode($json_string, true);
		return isset($json['count']) ? intval($json['count']) : 0;
	}

	function getLinkedin($url)
	{
		$json_string = $this->fileGetContentsCurl("http://www.linkedin.com/countserv/count/share?url=$this->rawUrlEncode($url)&format=json");
		$json = json_decode($json_string, true);
		return isset($json['count']) ? intval($json['count']) : 0;
	}

	function getFb($url)
	{
		$json_string = $this->fileGetContentsCurl('http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' . $this->rawUrlEncode($url));
		$json = json_decode($json_string, true);
        
		return isset($json[0]['total_count']) ? intval($json[0]['total_count']) : 0;
		
		/*
		
		$json[0] = array(9) {
		  ["url"] => string(124) "https://www.paypal-community.com/t5/PayPal-Forward/Beaming-to-Bitcoin-PayPal-s-15-years-of-Progress-Payments-and/ba-p/892151"
		  ["normalized_url"] => string(124) "https://www.paypal-community.com/t5/PayPal-Forward/Beaming-to-Bitcoin-PayPal-s-15-years-of-Progress-Payments-and/ba-p/892151"
		  ["share_count"] => int(30)
		  ["like_count"] => int(45)
		  ["comment_count"] => int(4)
		  ["total_count"] => int(79)
		  ["click_count"] => int(0)
		  ["comments_fbid"] => int(904803986210305)
		  ["commentsbox_count"] => int(0)
		}
		
		*/
	}
	
	function getFbShares($url)
	{
		$json_string = $this->fileGetContentsCurl('http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' . $this->rawUrlEncode($url));
		$json = json_decode($json_string, true);
		return isset($json[0]['share_count']) ? intval($json[0]['share_count']) : 0;
	}

	function getPlusones($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . rawurldecode($this->rawUrlEncode($url)) . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		$curl_results = curl_exec($curl);
		curl_close($curl);
		$json = json_decode($curl_results, true);
        //Zend_Debug::dump($json);
		return isset($json[0]['result']['metadata']['globalCounts']['count']) ? intval($json[0]['result']['metadata']['globalCounts']['count']) : 0;
	}

	function getStumble($url)
	{
		$json_string = $this->fileGetContentsCurl('http://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $this->rawUrlEncode($url));
		$json = json_decode($json_string, true);
		return isset($json['result']['views']) ? intval($json['result']['views']) : 0;
	}

	function getDelicious($url)
	{
		$json_string = $this->fileGetContentsCurl('http://feeds.delicious.com/v2/json/urlinfo/data?url=' . $this->rawUrlEncode($url));
		$json = json_decode($json_string, true);
		return isset($json[0]['total_posts']) ? intval($json[0]['total_posts']) : 0;
	}

	function getPinterest($url)
	{
		$return_data = $this->fileGetContentsCurl('http://api.pinterest.com/v1/urls/count.json?url=' . $this->rawUrlEncode($url));
		$json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $return_data);
		$json = json_decode($json_string, true);
		return isset($json['count']) ? intval($json['count']) : 0;
	}
	
	private function rawUrlEncode($url)
	{
		return rawurlencode($url);
	}

	private function fileGetContentsCurl($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		$cont = curl_exec($ch);
        
        //Zend_Debug::dump($cont); die();
		
		if (curl_error($ch)) {
			//die(curl_error($ch));
			return 0;
		}
		
		return $cont;
	}
}
