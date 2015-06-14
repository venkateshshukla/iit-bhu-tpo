<?php 
require_once(SBSERVICE);

/**
 *	@class KeyAvailableWorkflow
 *	@desc Checks for availability of service key value
 *
 *	@param user string Username [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class KeyAvailableWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('user')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'User available for registration';
		
		$service = array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('user'),
			'conn' => 'cbconn',
			'relation' => '`keys`',
			'sqlprj' => 'keyid',
			'sqlcnd' => "where `user`='\${user}'",
			'escparam' => array('user'),
			'not' => false,
			'errormsg' => 'Username already registered'
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