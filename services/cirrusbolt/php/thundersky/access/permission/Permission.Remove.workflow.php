<?php 
require_once(SBSERVICE);

/**
 *	@class PermissionRemoveWorkflow
 *	@desc Removes permission by ID
 *
 *	@param refid long int Reference ID [memory]
 *	@param member string Member username [memory]
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PermissionRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'refid', 'member')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Permission removed successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'refid'),
			'init' => false,
			'self' => true
		),
		array(
			'service' => 'guard.member.revoke.workflow',
			'input' => array('chainid' => 'refid', 'user' => 'member'),
		),
		array(
			'service' => 'guard.chain.track.workflow',
			'input' => array('child' => 'refid', 'user' => 'member'),
			'verb' => 'deleted permission',
			'join' => 'of',
			'public' => 0,
			'output' => array('id' => 'trackid')
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