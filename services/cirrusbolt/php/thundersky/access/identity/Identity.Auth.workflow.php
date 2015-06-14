<?php 
require_once(SBSERVICE);

/**
 *	@class IdentityAuthWorkflow
 *	@desc Return keyid after authorization
 *
 *	@param keyid long int Key ID [memory]
 *	@param user string User name [memory]
 *	@param name string Person username [memory] optional default user
 *
 *	@return keyid long int Key ID [memory]
 *	@return username string Person username [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class IdentityAuthWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user'),
			'optional' => array('name' => false),
			'set' => array('name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		
		$workflow = array(
		array(
			'service' => 'people.person.find.workflow'
		),
		array(
			'service' => 'cbcore.data.equal.service',
			'input' => array('data' => 'admin'),
			'value' => true,
			'errormsg' => 'Not Authorized',
			'errstatus' => 407
		),
		array(
			'service' => 'guard.key.identify.workflow',
			'input' => array('user' => 'username'),
			'keyid' => false,
			'context' => CONTEXT,
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('keyid', 'username');
	}
	
}

?>