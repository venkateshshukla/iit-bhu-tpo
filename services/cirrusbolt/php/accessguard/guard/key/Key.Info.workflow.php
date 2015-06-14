<?php 
require_once(SBSERVICE);

/**
 *	@class KeyInfoWorkflow
 *	@desc Returns key information for key ID
 *
 *	@param keyid long int Key ID [memory]
 *
 *	@return user string Key user [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class KeyInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Key information given successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('keyid'),
			'conn' => 'cbconn',
			'relation' => '`keys`',
			'sqlprj' => 'user',
			'sqlcnd' => "where `keyid`=\${keyid}",
			'errormsg' => 'Invalid Key ID'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0.user' => 'user')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('user');
	}
	
}

?>