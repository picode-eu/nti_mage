<?php
class Picode_ProviderReputation_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $timeout;
    
    function __construct($timeout = 10)
    {
        $this->timeout = $timeout;
    }
	
	public function getSocialShare($network, $providerUrl)
	{
		$socialCount = 0;
		
		switch ($network) {
			case 'facebook':
				$json_string = $this->fileGetContentsCurl('http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' . $this->rawUrlEncode($providerUrl));
        		$json = json_decode($json_string, true);
				$socialCount = isset($json[0]['total_count']) ? intval($json[0]['total_count']) : 0;
				
				break;
				
			case 'gplus':
                /* dosen't work !!!
                // return the number of public shares for a given URL
                $shares_url = 'https://plus.google.com/ripple/details?url='. $this->rawUrlEncode($providerUrl);
                $response = file_get_contents($shares_url);
                $shares_match = preg_match('@([0-9]+) public shares@',$response,$matches);
                $socialCount = $matches[1];
                */
                
                // Getting the Google+ Share Count
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . rawurldecode($this->rawUrlEncode($providerUrl)) . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
				$curl_results = curl_exec($curl);
				curl_close($curl);
				$json = json_decode($curl_results, true);
		        //Zend_Debug::dump($json);
				$socialCount = isset($json[0]['result']['metadata']['globalCounts']['count']) ? intval($json[0]['result']['metadata']['globalCounts']['count']) : 0;

				break;
				
			case 'tweets':
				$json_string = $this->fileGetContentsCurl('http://urls.api.twitter.com/1/urls/count.json?url=' . $this->rawUrlEncode($providerUrl));
				$json = json_decode($json_string, true);
				$socialCount = isset($json['count']) ? intval($json['count']) : 0;
				
				break;
		}

		return $socialCount;
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
        
        if (curl_error($ch)) {
            return 0;
        }
        
        return $cont;
    }
}
