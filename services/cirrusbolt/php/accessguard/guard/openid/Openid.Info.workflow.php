<?php 
require_once(SBSERVICE);

/**
 *	@class OpenidInfoWorkflow
 *	@desc Returns openid information in chain
 *
 *	@param oid long int Openid ID [memory]
 *	@param keyid long int Key ID [memory]
 *
 *	@return openid array Openid email information [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class OpenidInfoWorkflow implements Service {
	
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
		$memory['msg'] = 'Openid information returned successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('oid', 'keyid'),
			'conn' => 'cbconn',
			'relation' => '`openids`',
			'sqlprj' => '`oid`, `email`',
			'sqlcnd' => "where `oid`=\${oid} and `keyid`=\${keyid}",
			'errormsg' => 'Invalid Openid ID'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0' => 'openid')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('openid');
	}
	
}

?>