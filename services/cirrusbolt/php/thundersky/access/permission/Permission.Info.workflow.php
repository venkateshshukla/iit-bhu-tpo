<?php 
require_once(SBSERVICE);

/**
 *	@class PermissionInfoWorkflow
 *	@desc Returns permission information by ID
 *
 *	@param ckid/id Permission chainkeyid [memory]
 *	@param member string Permission user [memory]
 *	@param keyid long int Usage Key ID [memory] 
 *	@param user string Key User [memory]
 *	@param refid long int Reference ID [memory] optional default 0
 *	@param rfname/name string Reference name [memory] optional default ''
 *
 *	@return permission array Permission information [memory]
 *	@return rfname string Reference name [memory]
 *	@return refid long int Reference ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PermissionInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('cmtid', 'member'),
			'optional' => array('keyid' => false, 'user' => '', 'rfname' => false, 'name' => '', 'refid' => false, 'id' => 0),
			'set' => array('id', 'name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['cmtid'] = $memory['cmtid'] ? $memory['cmtid'] : $memory['id'];
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'refid'),
			'init' => false,
			'self' => true
		),
		array(
			'service' => 'guard.member.info.workflow',
			'input' => array('chainid' => 'refid', 'user' => 'member'),
			'output' => array('result' => 'permission')
		),
		array(
			'service' => 'guard.chain.track.workflow',
			'input' => array('child' => 'refid'),
			'verb' => 'viewed',
			'join' => 'from',
			'public' => 0,
			'output' => array('id' => 'trackid')
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		$memory['admin'] = 1;
		
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('permission', 'rfname', 'refid', 'admin');
	}
	
}

?>