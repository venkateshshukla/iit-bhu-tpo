<?php 
require_once(SBSERVICE);

/**
 *	@class PermissionListWorkflow
 *	@desc Returns all permissions information in reference
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string User [memory]
 *	@param refid/id long int Reference ID [memory] optional default 0
 *	@param rfname/name string Reference name [memory] optional default ''
 *	@param state string State [memory] optional default false
 *
 *	@param pgsz long int Paging Size [memory] optional default 50
 *	@param pgno long int Paging Index [memory] optional default 0
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return permissions array Permissions information [memory]
 *	@return refid long int Reference ID [memory]
 *	@return rfname string Reference Name [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return pgsz long int Paging Size [memory]
 *	@return pgno long int Paging Index [memory]
 *	@return total long int Paging Total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PermissionListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'refid' => false, 'id' => 0, 'rfname' => false, 'name' => '', 'pgsz' => 50, 'pgno' => 0, 'total' => false, 'padmin' => true, 'state' => false),
			'set' => array('id', 'name', 'state')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['refid'] = $memory['refid'] ? $memory['refid'] : $memory['id'];
		$memory['rfname'] = $memory['rfname'] ? $memory['rfname'] : $memory['name'];
		$memory['msg'] = 'Permissions information given successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'refid'),
			'action' => 'list'
		),
		array(
			'service' => 'guard.member.all.workflow',
			'input' => array('chainid' => 'refid'),
			'output' => array('result' => 'members'),
			'mapname' => 'permission'
		),
		array(
			'service' => 'cbcore.data.merge.service',
			'args' => array('members'),
			'params' => array('members' => array(0, 'permission')),
			'output' => array('result' => 'permissions')
		),
		array(
			'service' => 'guard.chain.track.workflow',
			'input' => array('child' => 'refid'),
			'verb' => 'enlisted',
			'join' => 'from',
			'public' => 0,
			'output' => array('id' => 'trackid')
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'refid'),
			'output' => array('chain' => 'pchain')
		),
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'refid'),
			'init' => false,
			'self' => true,
			'admin' => true,
			'output' => array('admin' => 'padmin')
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		$memory['admin'] = 1;
		
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('permissions', 'refid', 'rfname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz');
	}
	
}

?>