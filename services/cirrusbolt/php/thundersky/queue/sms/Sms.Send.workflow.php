<?php 
require_once(SBSERVICE);

/**
 *	@class SmsSendWorkflow
 *	@desc Sends sms by ID
 *
 *	@param smsid/id string SMS ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param queid long int Queue ID [memory] optional default 0
 *
 *	@return sms array SMS information [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SmsSendWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('queid' => 0, 'smsid' => false, 'id' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'SMS Sent Successfully';
		$memory['smsid'] = $memory['smsid'] ? $memory['smsid'] : $memory['id'];
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'smsid', 'parent' => 'queid'),
			'action' => 'send',
			'conn' => 'cbqconn',
			'relation' => '`sms`',
			'sqlcnd' => "where `smsid`='\${id}'",
			'errormsg' => 'Invalid SMS ID',
			'successmsg' => 'SMS information given successfully',
			'output' => array('entity' => 'sms'),
			'track' => false,
			'chadm' => false,
			'mgchn' => false,
			'cache' => false
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('sms'),
			'params' => array('sms.to' => 'to', 'sms.from' => 'from', 'sms.body' => 'body')
		),
		array(
			'service' => 'queue.sms.send.service'
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		$valid = $memory['valid'];
		$msg = $memory['msg'];
		$status = $memory['status'];
		$details = $memory['details'];
		
		$memory['msg'] = 'SMS Sent Successfully';
		$service = array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('smsid', 'smsstatus', 'response'),
			'smsstatus' => $status,
			'response' => $details,
			'conn' => 'cbqconn',
			'relation' => '`sms`',
			'sqlcnd' => "set `status`=\${smsstatus}, `response`='\${response}', `stime`=now(), `count`=`count`+1 where `smsid`=\${smsid}",
			'escparam' => array('response'),
			'errormsg' => 'Invalid SMS ID',
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
		return array('sms', 'queid');
	}
	
}

?>
