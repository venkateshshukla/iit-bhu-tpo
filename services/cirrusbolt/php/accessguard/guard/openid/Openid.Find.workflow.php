<?php 
require_once(SBSERVICE);

/**
 *	@class OpenidFindWorkflow
 *	@desc Returns openid information in chain
 *
 *	@param email string Openid email [memory]
 *
 *	@return openid array Openid email information [memory]
 *	@return keyid long int Openid key [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class OpenidFindWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('email')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Openid information returned successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('email'),
			'conn' => 'cbconn',
			'relation' => '`openids`',
			'sqlcnd' => "where `email`='\${email}'",
			'escparam' => array('email'),
			'errormsg' => 'Invalid Openid Email'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0' => 'openid', 'result.0.keyid' => 'keyid')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('openid', 'keyid');
	}
	
}

?>