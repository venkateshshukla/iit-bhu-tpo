<?php 
require_once(SBSERVICE);

/**
 *	@class SmsAddWorkflow
 *	@desc Adds new sms to queue
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
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SmsAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'to', 'body'),
			'optional' => array('queid' => 0, 'owner' => false, 'from' => '')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.add.workflow',
			'args' => array('to', 'from', 'body'),
			'input' => array('parent' => 'queid'),
			'authorize' => 'info:edit:send:add:remove:list',
			'conn' => 'cbqconn',
			'relation' => '`sms`',
			'sqlcnd' => "(`smsid`, `owner`, `to`, `from`, `body`) values (\${id}, \${owner}, '\${to}', '\${from}', '\${body}')",
			'escparam' => array('to', 'from', 'body'),
			'type' => 'sms',
			'successmsg' => 'SMS saved successfully',
			'output' => array('id' => 'smsid')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('smsid');
	}
	
}

?>