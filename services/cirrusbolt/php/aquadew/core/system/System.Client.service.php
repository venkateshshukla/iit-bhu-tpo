<?php 
require_once(SBSERVICE);

/**
 *	@class SystemClientService
 *	@desc Returns client host address and proxy if any
 *
 *	@return client string Client address [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class SystemClientService implements Service {
	
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
		$memory['client' =] = '';
	
		if(isset($_SERVER['REMOTE_ADDR']))
			$memory['client'] = $_SERVER['REMOTE_ADDR'];
			
		if(isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
			$memory['client'] .= " / ".$_SERVER["HTTP_X_FORWARDED_FOR"];
		
		$memory['valid'] = true;
		$memory['msg'] = 'Valid Client Address Given';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('client');
	}
	
}

?>