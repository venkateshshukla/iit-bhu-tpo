<?php 
require_once(SBSERVICE);

/**
 *	@class PersonAvailableWorkflow
 *	@desc Checks availability of person username
 *
 *	@param username string Person username [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PersonAvailableWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('username')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Available for Registration';
		
		$service = array(
			'service' => 'guard.key.available.workflow',
			'input' => array('user' => 'username')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>