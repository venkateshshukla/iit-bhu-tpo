<?php 
require_once(SBSERVICE);

/**
 *	@class PermissionAddWorkflow
 *	@desc Adds new permission
 *
 *	@param member string Member username [memory]
 *	@param control array Permission control [memory]
 *	@param inherit boolean Permission inherit [memory] optional default false
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param refid long int Reference ID [memory] optional default 0
 *	@param rfname string Reference Name [memory] optional default ''
 *	@param level integer Web level [memory] optional default false (inherit post admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
 *
 *	@return ckid long int Permission Chainkey ID [memory]
 *	@return refid long int Reference ID [memory]
 *	@return rfname string Reference Name [memory]
 *	@return permission array Permission information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PermissionAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'member'),
			'optional' => array('refid' => 0, 'rfname' => '', 'level' => false, 'owner' => false, 'control' => array(), 'inherit' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		if($memory['inherit']){
			$memory['control'] = false;
		}
		else {
			$memory['control'] = implode(':', $memory['control']);
		}
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'refid'),
			'init' => false,
			'self' => true
		),
		array(
			'service' => 'guard.member.grant.workflow',
			'input' => array('chainid' => 'refid', 'user' => 'member'),
			'output' => array('id' => 'ckid')
		),
		array(
			'service' => 'guard.member.info.workflow',
			'input' => array('chainid' => 'refid', 'user' => 'member'),
			'output' => array('result' => 'permission')
		),
		array(
			'service' => 'guard.chain.track.workflow',
			'input' => array('child' => 'refid'),
			'verb' => 'added permission',
			'join' => 'from',
			'public' => 0,
			'output' => array('id' => 'trackid')
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid']){
			if($memory['status'] == 585) $memory['msg'] = 'Invalid Username / Duplicate Permission';
			return $memory;
		}
		
		$memory['padmin'] = $memory['admin'] = 1;
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('ckid', 'member', 'refid', 'rfname', 'permission', 'admin', 'padmin');
	}
	
}

?>