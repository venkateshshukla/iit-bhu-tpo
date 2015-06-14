<?php 
require_once(SBSERVICE);

/**
 *	@class MailAddWorkflow
 *	@desc Adds new mail to queue
 *
 *	@param to string To address [memory]
 *	@param subject string Subject [memory] 
 *	@param body string Message Body [memory] 
 *	@param attach array Attachments [memory] optional default array()
 *	@param custom array Headers [memory] optional default array()
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param queid long int Queue ID [memory] optional default 0
 *	@param owner long int Owner Key ID [memory] optional default keyid
 *
 *	@return mailid long int Mail ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class MailAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'to', 'subject', 'body'),
			'optional' => array('attach' => array(), 'custom' => array(), 'queid' => 0, 'owner' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['attach'] = json_encode($memory['attach']);
		$memory['custom'] = json_encode($memory['custom']);
		
		$service = array(
			'service' => 'transpera.entity.add.workflow',
			'args' => array('to', 'subject', 'body', 'attach', 'custom'),
			'input' => array('parent' => 'queid'),
			'authorize' => 'info:edit:send:add:remove:list',
			'conn' => 'cbqconn',
			'relation' => '`mails`',
			'sqlcnd' => "(`mailid`, `owner`, `to`, `subject`, `body`, `attach`, `custom`) values (\${id}, \${owner}, '\${to}', '\${subject}', '\${body}', '\${attach}', '\${custom}')",
			'escparam' => array('to', 'subject', 'body', 'attach', 'custom'),
			'type' => 'mail',
			'successmsg' => 'Mail added successfully',
			'output' => array('id' => 'mailid')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('mailid');
	}
	
}

?>