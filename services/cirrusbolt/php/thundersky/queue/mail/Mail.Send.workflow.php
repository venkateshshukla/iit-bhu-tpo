<?php 
require_once(SBSERVICE);

/**
 *	@class MailSendWorkflow
 *	@desc Sends mail information by ID
 *
 *	@param mailid/id string Mail ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param queid long int Queue ID [memory] optional default 0
 *	@param custom array Headers [memory] optional default array()
 *
 *	@return mail array Mail information [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class MailSendWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('queid' => 0, 'mailid' => false, 'id' => 0, 'custom' => array())
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['mailid'] = $memory['mailid'] ? $memory['mailid'] : $memory['id'];
		$memory['msg'] = 'Mail Sent Successfully';
		$config = Snowblozm::get('mail');
		$type = $config['type'];
		//$memory['custom'][0] = 'X-Priority: 1';
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'mailid', 'parent' => 'queid'),
			'action' => 'send',
			'conn' => 'cbqconn',
			'relation' => '`mails`',
			'sqlcnd' => "where `mailid`='\${id}'",
			'errormsg' => 'Invalid Mail ID',
			'successmsg' => 'Mail information given successfully',
			'output' => array('entity' => 'mail'),
			'track' => false,
			'chadm' => false,
			'mgchn' => false,
			'cache' => false
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('mail'),
			'params' => array('mail.to' => 'to', 'mail.subject' => 'subject', 'mail.body' => 'body', 'mail.attach' => 'attach')
		),
		array(
			'service' => 'cbcore.data.decode.service',
			'input' => array('data' => 'attach'),
			'type' => 'json',
			'output' => array('result' => 'attach')
		),
		array(
			'service' => "queue.mail.$type.service"
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		$valid = $memory['valid'];
		$msg = $memory['msg'];
		$status = $memory['status'];
		$details = $memory['details'];
		
		if($memory['valid'])
			$memory['mail']['attach'] = $memory['attach'];
		
		$service = array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('mailid', 'mailstatus', 'response'),
			'mailstatus' => $memory['status'],
			'response' => $memory['details'],
			'conn' => 'cbqconn',
			'relation' => '`mails`',
			'sqlcnd' => "set `status`=\${mailstatus}, `response`='\${response}', `stime`=now(), `count`=`count`+1 where `mailid`=\${mailid}",
			'escparam' => array('response'),
			'errormsg' => 'Invalid Mail ID',
			'strict' => false
		);
		
		$memory = Snowblozm::run($service, $memory);
		
		$memory['valid'] = $valid;
		$memory['msg'] = $msg;
		$memory['status'] = $status;
		$memory['details'] = $details;
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('mail', 'queid');
	}
	
}

?>
