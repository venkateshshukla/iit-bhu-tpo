<?php 
require_once(SBSERVICE);

/**
 *	@class CurlExecuteService
 *	@desc Executes cURL request and returns response
 *
 *	@param url string URL [memory]
 *	@param post boolean [memory] optional default false
 *	@param data array/string Data to send with request [memory] optional default array()
 *	@param plain boolean [memory] optional default false
 *
 *	@return response string Response [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class CurlExecuteService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('url'),
			'optional' => array('post' => false, 'data' => array(), 'plain' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$url = $memory['url'];
		$data = $memory['data'];
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		if($memory['post']){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		
		if($memory['plain'])
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain')); 
			
		$result = curl_exec ($ch);
		$info = curl_getinfo($ch);
		
		if ($result === false || $info['http_code'] != 200){
			$memory['valid'] = false;
			$memory['msg'] = 'Error in cURL';
			$memory['status'] = $info['http_code'];
			$memory['details'] = 'Curl error : '.curl_error($ch).' @curl.execute.service';
			curl_close($ch);
			return $memory;
		}
		
		curl_close($ch);
		$memory['response'] = $result;
		$memory['valid'] = true;
		$memory['msg'] = 'Valid cURL Execution';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('response');
	}
	
}

?>