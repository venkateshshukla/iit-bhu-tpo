<?php 
require_once(SBSERVICE);

/**
 *	@class MailEditWorkflow
 *	@desc Edits mail using ID
 *
 *	@param mailid long int Mail ID [memory]
 *	@param to string To address [memory]
 *	@param subject string Subject [memory] 
 *	@param body string Message Body [memory] 
 *	@param attach array Attachments [memory] optional default array()
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class MailEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'mailid', 'to', 'subject', 'body'),
			'optional' => array('attach' => array())
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['attach'] = json_encode($memory['attach']);
		
		$service = array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('to', 'subject', 'body', 'attach'),
			'input' => array('id' => 'mailid'),
			'conn' => 'cbqconn',
			'relation' => '`mails`',
			'sqlcnd' => "set `to`='\${to}', `subject`='\${subject}', `body`='\${body}', `attach`='\${attach}' where `mailid`=\${id} and `state`=0",
			'escparam' => array('to', 'subject', 'body', 'attach'),
			'errormsg' => 'Mail Already Sent / Invalid Mail ID',
			'successmsg' => 'Mail edited successfully'
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