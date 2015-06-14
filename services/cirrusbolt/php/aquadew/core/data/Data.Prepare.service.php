<?php 
require_once(SBSERVICE);

/**
 *	@class DataPrepareService
 *	@desc Prepares data as array from memory
 *
 *	@param data array Stream data [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class DataPrepareService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array();
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$result = $memory;
		if(isset($result['service'])) unset($result['service']);
		if(isset($result['args'])) unset($result['args']);
		if(isset($result['strict'])) unset($result['strict']);
		if(isset($result['output'])) unset($result['output']);
		if(isset($result['message']['keyid'])) unset($result['message']['keyid']);
		if(isset($result['message']['user'])) unset($result['message']['user']);
		if(isset($result['message']['pages'])) unset($result['message']['pages']);
		if(isset($result['message']['emergency'])) unset($result['message']['emergency']);
		
		$result['valid'] = $result['message']['valid'];
		$result['msg'] = $result['message']['msg'];
		$result['status'] = $result['message']['status'];
		$result['details'] = $result['message']['details'];
		
		$memory['result'] = $result;
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
		return array('result');
	}
	
}

?>