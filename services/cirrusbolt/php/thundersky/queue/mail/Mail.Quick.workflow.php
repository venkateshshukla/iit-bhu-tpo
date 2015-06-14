<?php 
require_once(SBSERVICE);

/**
 *	@class MailQuickWorkflow
 *	@desc Adds new mail to queue and sends it
 *
 *	@param to string To address [memory] optional default false
 *	@param to_select array
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
			'required' => array('keyid', 'user', 'subject', 'body'),
			'optional' => array(
				'to' => false, 
				'to_args' => array(),
				'to_relation' => '`persons`', 
				'to_conn' => 'cbpconn', 
				'to_sqlcnd' => false,
				'to_sqlprj' => '`email`',
				'to_errormsg' => 'Error Selecting Data',
				'to_escparam' => array(),
				'to_key' => 'email',
				'attach' => array(), 
				'atch_args' => array(),
				'atch_relation' => '`files` f, `directories` d', 
				'atch_conn' => 'cbsconn', 
				'atch_sqlcnd' => false,
				'atch_sqlprj' => 'concat(d.`path`, f.`filename`) as `filepath`',
				'atch_errormsg' => 'Error Selecting Data',
				'atch_escparam' => array(),
				'atch_key' => 'filepath',
				'attach_select' => false, 
				'custom' => array(), 
				'queid' => 0, 
				'owner' => false
			)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$workflow = array();
		
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
		
		if($memory['atch_sqlcnd']){
			array_push($workflow, array(
				'service' => 'transpera.relation.select.workflow',				
				'args' => $memory['atch_args']),
				'input' => array('errormsg' => 'atch_errormsg', 'relation' => 'atch_relation', 'conn' => 'atch_conn', 'escparam' => 'atch_escparam', 'sqlprj' => 'atch_sqlprj', 'sqlcnd' => 'atch_sqlcnd', ''),
				'ismap' => false
			),
			array(
				'service' => 'cbcore.data.list.service',
				'args' => array('result'),
				'input' => array('attr' => 'atch_key'),
				'output' => array('result' => 'attach')
			));
		}
		
		$memory['attach'] = json_encode($memory['attach']);
		$memory['custom'] = json_encode($memory['custom']);
		
		$workflow = array(
		array(
			'service' => 'queue.mail.add.workflow'
		),
		array(
			'service' => 'queue.mail.send.workflow'
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('mailid', 'mail', 'queid');
	}
	
}

?>