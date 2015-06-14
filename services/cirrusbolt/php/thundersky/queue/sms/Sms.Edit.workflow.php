<?php 
require_once(SBSERVICE);

/**
 *	@class SmsEditWorkflow
 *	@desc Edits sms using ID
 *
 *	@param smsid long int SMS ID [memory]
 *	@param to string To address [memory]
 *	@param from string Sender [memory] optional default ''
 *	@param body string Message Body [memory] 
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SmsEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'smsid', 'to', 'body'),
			'optional' => array( 'from' => '')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('to', 'from', 'body'),
			'input' => array('id' => 'smsid'),
			'conn' => 'cbqconn',
			'relation' => '`sms`',
			'sqlcnd' => "set `to`='\${to}', `from`='\${from}', `body`='\${body}' where `smsid`=\${id} and `status`=0",
			'escparam' => array('to', 'from', 'subject', 'body'),
			'errormsg' => 'SMS Already Sent / Invalid SMS ID',
			'successmsg' => 'SMS edited successfully'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>