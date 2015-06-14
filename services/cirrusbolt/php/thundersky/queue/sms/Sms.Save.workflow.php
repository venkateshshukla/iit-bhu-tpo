<?php 
require_once(SBSERVICE);

/**
 *	@class SmsSaveWorkflow
 *	@desc Saves sms by adding new one to queue if not exists
 *
 *	@param to string To address [memory]
 *	@param from string Sender [memory] 
 *	@param body string Message Body [memory] 
 *	@param keyid long int Usage Key ID [memory]
 *	@param queid long int Queue ID [memory] optional default 0
 *	@param owner long int Owner Key ID [memory] optional default keyid
 *
 *	@return smsid long int SMS ID [memory]
 *	@return sms array SMS information [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SmsSaveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'to', 'body'),
			'optional' => array('queid' => 0, 'owner' => false, 'from' => '')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'queue.sms.info'
		);
	
	
		$workflow = array(
		array(
			'service' => 'queue.sms.add.workflow'
		),
		array(
			'service' => 'queue.sms.send.workflow'
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('smsid', 'sms', 'queid');
	}
	
}

?>
