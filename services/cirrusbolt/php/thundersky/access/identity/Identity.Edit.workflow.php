<?php 
require_once(SBSERVICE);

/**
 *	@class IdentityEditWorkflow
 *	@desc Edits openid key of Chain
 *
 *	@param email string Email ID [memory]
 *	@param keyid long int Key ID [memory]
 *	@param oid long int Identity ID [memory]
 *	@param user string User name [memory]
 *	@param name string Person Name [memory] optional default user
 *
 *	@return identity array Identity email information [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class IdentityEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'oid', 'email', 'user'),
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
			'service' => 'guard.openid.edit.workflow'
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
		return array('identity', 'name');
	}
	
}

?>