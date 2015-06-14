<?php 
require_once(SBSERVICE);

/**
 *	@class KeyAuthenticateWorkflow
 *	@desc Validates user keyvalue and selects key ID
 *
 *	@param user string Username [memory]
 *	@param key string Usage key [memory]
 *	@param context string Application context for user [memory] optional default false
 *
 *	@return keyid long int Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class KeyAuthenticateWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('key', 'user'),
			'optional' => array('context' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Key authenticated successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('key', 'user', 'context'),
			'conn' => 'cbconn',
			'relation' => '`keys`',
			'sqlprj' => 'keyid',
			'sqlcnd' => "where `user`='\${user}' and `context` like '%\${context}%' and `keyvalue`=MD5('\${user}\${key}')",
			'escparam' => array('key', 'user', 'context'),
			'errormsg' => 'Invalid Credentials'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0.keyid' => 'keyid')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('keyid');
	}
	
}

?>