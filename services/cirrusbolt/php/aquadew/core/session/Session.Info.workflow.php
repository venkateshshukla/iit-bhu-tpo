<?php 
require_once(SBSERVICE);

/**
 *	@class SessionInfoWorkflow
 *	@desc Returns session owner email by ID
 *
 *	@param sessionid string Resource ID [memory]
 *
 *	@return user string Session owner [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SessionInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('sessionid')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Session information given successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.relation.delete.workflow',
			'conn' => 'cbconn',
			'relation' => '`sessions`',
			'sqlcnd' => "where `expiry` < now()",
			'errormsg' => 'Invalid Session ID',
			'check' => false
		),
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('sessionid'),
			'conn' => 'cbconn',
			'relation' => '`sessions`',
			'sqlcnd' => "where `sessionid`='\${sessionid}'",
			'escparam' => array('sessionid'),
			'errormsg' => 'Invalid Session ID',
			'cache' => false
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