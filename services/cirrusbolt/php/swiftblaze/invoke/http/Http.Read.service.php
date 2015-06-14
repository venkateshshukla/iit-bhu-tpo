<?php 
require_once(SBSERVICE);

/**
 *	@class HttpReadService
 *	@desc Reads HTTP request from input stream
 *
 *	@param source string Source type [memory] optional default 'stream' ('stream', 'path_info', 'query_string')
 *	@param default string Default string [memory] optional default 'home'
 *
 *	@return data string Stream data [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class HttpReadService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('source' => 'stream', 'default' => 'home')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		switch($memory['source']){
			case 'query_string' :
				$memory['data'] = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '/'.$memory['default'];
				break;
			
			case 'path_info' :
				$memory['data'] = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/'.$memory['default'];
				break;
			
			case 'stream' :
			default :
				$memory['data'] = file_get_contents('php://input');
				break;
		}
		
		
		$memory['valid'] = true;
		$memory['msg'] = 'Valid Request';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('data');
	}
	
}

?>