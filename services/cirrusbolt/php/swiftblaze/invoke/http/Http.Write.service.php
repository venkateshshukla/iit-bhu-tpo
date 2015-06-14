<?php 
require_once(SBSERVICE);

/**
 *	@class HttpWriteService
 *	@desc Writes HTTP response to output stream
 *
 *	@param data string Stream data [memory]
 *	@param type string Request type [memory] optional default 'json' ('json', 'xml', 'wddx', 'plain', 'html')
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class HttpWriteService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('data'),
			'optional' => array('type' => 'json')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$type = Snowblozm::$setmime ? Snowblozm::$setmime : $memory['type'];
		
		switch($type){
			case 'json' :
				header('Content-type: application/json');
				break;
			case 'wddx' :
				header('Content-type: application/xml');
				break;
			case 'xml' :
				header('Content-type: text/xml');
				break;
			case 'html' :
				header('Content-type: text/html');
				break;
			case 'plain' :
			default: 
				header('Content-type: text/plain');
				break;
		}
		
		echo $memory['data'];

		$memory['valid'] = true;
		$memory['msg'] = 'Valid Response Given';
		$memory['status'] = 201;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>