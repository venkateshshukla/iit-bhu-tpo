<?php 
require_once(SBSERVICE);

/**
 *	@class IdentityAddWorkflow
 *	@desc Adds openid email identity
 *
 *	@param keyid long int Key ID [memory]
 *	@param email string Email ID [memory]
 *	@param user string User name [memory]
 *	@param name string Person Name [memory] optional default user
 *
 *	@return return oid long int Identity ID [memory]
 *	@return identity array Identity email information [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class IdentityAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'email', 'user'),
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
			'service' => 'guard.openid.add.workflow'
		),
		array(
			'service' => 'access.identity.info.workflow'
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('oid', 'identity', 'name');
	}
	
}

?>