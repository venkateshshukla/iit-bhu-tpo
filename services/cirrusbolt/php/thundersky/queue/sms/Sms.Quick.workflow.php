<?php 
require_once(SBSERVICE);

/**
 *	@class SmsQuickWorkflow
 *	@desc Sends sms by adding new one to queue
 *
 *	@param to string To address [memory]
 *	@param from string Sender [memory] 
 *	@param body string Message Body [memory] 
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
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
class SmsQuickWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'body'),
			'optional' => array(
				'queid' => 0, 
				'to' => false, 
				'to_args' => array(),
				'to_relation' => '`persons`', 
				'to_conn' => 'cbpconn', 
				'to_sqlcnd' => false,
				'to_sqlprj' => '`phone`',
				'to_errormsg' => 'Error Selecting Data',
				'to_escparam' => array(),
				'to_key' => 'phone',
				'owner' => false, 
				'from' => ''
			)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		if($memory['to_sqlcnd']){
			array_push($workflow, array(
				'service' => 'transpera.relation.select.workflow',				
				'args' => $memory['to_args']),
				'input' => array('errormsg' => 'to_errormsg', 'relation' => 'to_relation', 'conn' => 'to_conn', 'escparam' => 'to_escparam', 'sqlprj' => 'to_sqlprj', 'sqlcnd' => 'to_sqlcnd', ''),
				'ismap' => false
			),
			array(
				'service' => 'cbcore.data.list.service',
				'args' => array('result'),
				'input' => array('attr' => 'to_key'),
				'output' => array('list' => 'to')
			));
		}
		
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
