<?php 
require_once(SBSERVICE);

/**
 *	@class IdentityListWorkflow
 *	@desc Returns openid key IDs in chain
 *
 *	@param keyid long int Key ID [memory]
 *	@param user string User name [memory]
 *	@param name string Person Name [memory] optional default user
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@return identities array Identity emails information [memory]
 *	@return name string Person Name [memory]
 *	@return total long int Paging total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class IdentityListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user'),
			'optional' => array('pgsz' => false, 'pgno' => 0, 'total' => false, 'name' => false),
			'set' => array('name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		
		$workflow = array(
		array(
			'service' => 'access.identity.auth.workflow',
			'output' => array('username' => 'name')
		),
		array(
			'service' => 'guard.openid.list.workflow',
			'output' => array('openids' => 'identities')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('identities', 'name', 'total');
	}
	
}

?>