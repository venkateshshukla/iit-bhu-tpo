<?php 
require_once(SBSERVICE);

/**
 *	@class IdentityRemoveWorkflow
 *	@desc Removes openid email
 *
 *	@param keyid long int Key ID [memory]
 *	@param email string Email ID [memory]
 *	@param user string User name [memory]
 *	@param name string Person Name [memory] optional default user
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class IdentityRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'email'),
			'optional' => array('name' => false)
		);
	}

	/**
	 *	@interface Service
	**/
	public function run($memory){
		$workflow = array(
		array(
			'service' => 'access.identity.auth.workflow'
		),
		array(
			'service' => 'guard.openid.remove.workflow'
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>