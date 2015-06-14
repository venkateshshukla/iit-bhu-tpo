<?php 
require_once(SBSERVICE);

function path_decode($data){
	$result = array();
	$args = explode('~', $data);
	
	$path = explode('/', $args[0]);
	$max = count($path);
	$i = 1; $j = 0;
	
	if($path[$max-1] == ''){
		unset($path[--$max]);
	}
	
	$index = $max -1;
	if($index > 2 && is_numeric($path[$index])){
		$result['service'] = $path[$index-1];
		$result[$j++] = $path[$index];
		$result[$j++] = '';
		$max = $index-1;
	}
	else if($index > 2 && is_numeric($path[$index-1])){
		$result['service'] = $path[$index-2];
		$result[$j++] = $path[$index-1];
		$result[$j++] = $path[$index];
		$max = $index-2;
	}
	elseif($index > 0) {
		$result['service'] = $path[$i++];
	}
	
	while($i < $max){
		$result[$j++] = $path[$i++];
	}
	
	if(isset($args[1])){
		$params = explode('/', $args[1]);
		$len = count($params);
		if($len % 2 && $params[$len-1]){
			$len--;
			$result['forward'] = $params[$len];
		}
		
		$len--;
		for($i=1; $i<$len; $i+=2){
			$result[$params[$i]] = $params[$i+1];
		}
	}
	
	return $result;
}

/**
 *	@class DataDecodeService
 *	@desc Decodes JSON XML WDDX data into array (supports copy of PATH, GET and POST data)
 *
 *	@param type string Request type [memory] optional default 'json' ('get', 'post', 'path', 'json', 'xml', 'wddx')
 *	@param data string Data to be decoded [memory] optional default '' when type=('get', 'post')
 *
 *	@return result array Decoded data [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class DataDecodeService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('type' => 'json', 'data' => '')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$type = $memory['type'];
		$data = $memory['data'];
		
		switch($type){
			case 'get' :
				$result = $_GET;
				break;
			case 'post' :
				$result = $_POST;
				break;
			case 'path' :
				$result = path_decode($data);
				break;
			case 'json' :
				$result = json_decode($data, true);
				break;
			case 'xml' :
				$result = (array) @simplexml_load_string($data);
				break;
			case 'wddx' :
				$result = wddx_deserialize($data);
				break;
			default :
				$memory['valid'] = false;
				$memory['msg'] = 'Invalid Data Type';
				$memory['status'] = 501;
				$memory['details'] = 'Data decoding not supported for type : '.$type.' @data.decode.service';
				return $memory;
		}
		
		if(($result === false || $result == null) && !is_array($result)){
			$memory['result'] = array();
			$memory['valid'] = false;
			$memory['msg'] = 'Unable to decode data';
			$memory['status'] = 501;
			$memory['details'] = 'Data could not be decoded @data.decode.service';
			return $memory;
		}
		
		$memory['result'] = $result;
		$memory['valid'] = true;
		$memory['msg'] = 'Valid Data Decoding';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('result');
	}
	
}

?>