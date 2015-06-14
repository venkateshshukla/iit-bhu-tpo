<?php 
require_once(SBSERVICE);

/**
 *	@class LaunchWorkflowWorkflow
 *	@desc Launches workflows from arrays
 *
 *	@param message array Message to be launched [memory]
 *	@param user string Username to be used if not set in message [memory] optional default false
 *	@param keyid long int Usage Key [memory] optional default false
 *	@param context string Usage Context [memory] optional default false
 *	@param type string response type [memory] ('json', 'wddx', 'xml', 'plain', 'html') optional default 'json'
 *
 *	@result result string Result [memory]
 *	@result message object Response [memory]
 *	@result keyid long int Usage Key [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class LaunchWorkflowWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('message'),
			'optional' => array('user' => false, 'keyid' => false, 'context' => false, 'type' => 'json')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		if($memory['keyid'] === false && $memory['user']){
			$memory = Snowblozm::run(array(
				'service' => 'guard.key.identify.workflow'
			), $memory);
			
			if(!$memory['valid'])
				return $memory;
		}
		
		$memory['message']['user'] = $memory['user'];
		$memory['message']['keyid'] = $memory['keyid'];
		$memory['message']['context'] = $memory['context'];
		
		$workflow = array(
		array(
			'service' => 'invoke.launch.message.service'
		),
		array(
			'service' => 'cbcore.data.prepare.service',
			'args' => array('message'),
			'strict' => false
		),
		array(
			'service' => 'cbcore.data.encode.service',
			'input' => array('data' => 'result')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('message', 'result', 'keyid');
	}
	
}

?>