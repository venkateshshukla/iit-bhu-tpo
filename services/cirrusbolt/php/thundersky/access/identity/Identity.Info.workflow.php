<?php 
require_once(SBSERVICE);

/**
 *	@class IdentityInfoWorkflow
 *	@desc Returns openid information in chain
 *
 *	@param oid long int Identity ID [memory]
 *	@param keyid long int Key ID [memory]
 *
 *	@return identity array Identity email information [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class IdentityInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('oid', 'keyid')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'guard.openid.info.workflow',
			'output' => array('openid' => 'identity')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('identity');
	}
	
}

?>