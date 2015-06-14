<?php 
require_once(SBSERVICE);

/**
 *	@class SessionAddWorkflow
 *	@desc Adds new session
 *
 *	@param user string Username [memory]
 *	@param expiry integer Days to expire [memory] optional default 1
 *
 *	@return sessionid string Session ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SessionAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('user'),
			'optional' => array('expiry' => 1)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Session added successfully';
		
		$workflow = array(
		array(
			'service' => 'cbcore.random.string.service',
			'length' => 54
		),
		array(
			'service' => 'cbcore.system.time.service'
		),
		array(
			'service' => 'cbcore.data.substitute.service',
			'args' => array('random', 'timestamp'),
			'data' => '${random}${timestamp}',
			'output' => array('result' => 'sessionid')
		),
		array(
			'service' => 'transpera.relation.insert.workflow',
			'args' => array('sessionid', 'user'),
			'conn' => 'cbconn',
			'relation' => '`sessions`',
			'sqlcnd' => "(`sessionid`, `user`, `expiry`) values ('\${sessionid}', '\${user}', (now() + interval ".$memory['expiry']." day))",
			'escparam' => array('sessionid', 'user')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('sessionid');
	}
	
}

?>