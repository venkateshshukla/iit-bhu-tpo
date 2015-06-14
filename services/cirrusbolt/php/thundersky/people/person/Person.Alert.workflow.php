<?php 
require_once(SBSERVICE);

/**
 *	@class PersonAlertWorkflow
 *	@desc Sends alert email by membership
 *
 *	@param chainid long int Chain ID [memory]
 *	@param queid long int Queue ID [memory]
  *	@param state string State [memory] optional default false (true= Not '0')
 *	@param subject string Subject [memory] 
 *	@param body string Message Body [memory] 
 *	@param attach array Attachments [memory] optional default array()
 *	@param custom array Headers [memory] optional default array()
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param device string Device [memory] optional default 'mail' ('mail', 'sms')
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PersonAlertWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'chainid', 'queid', 'subject', 'body'),
			'optional' => array('attach' => array(), 'custom' => array(), 'state' => false, 'device' => 'mail')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Alert Sent Successfully';
		$item = $memory['device'] == 'mail' ? 'email' : 'phone';
		
		$workflow = array(
		array(
			'service' => 'guard.member.list.workflow'
		),
		array(
			'service' => 'cbcore.data.list.service',
			'args' => array('result'),
			'attr' => 'keyid',
			'mapname' => 'data',
			'default' => array(-1)
		),
		array(
			'service' => 'transpera.relation.select.workflow',
			'args' => array('list'),
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'sqlprj' => "`$item`",
			'sqlcnd' => "where `owner` in \${list}",
			'escparam' => array('list'),
			'errormsg' => 'None Found to Alert',
			'mapkey' => $item
		),
		array(
			'service' => 'cbcore.data.list.service',
			'args' => array('result'),
			'attr' => $item,
			'mapname' => 'data'
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
		
		$to = implode(',', $memory['result']);
		
		switch($memory['device']){
			case 'mail' :
				$workflow = array(
				array(
					'service' => 'queue.mail.add.workflow',
					'to' => $to
				), 
				array(
					'service' => 'queue.mail.send.workflow',
				));
				break;
			
			case 'sms' :
				$workflow = array(
				array(
					'service' => 'queue.sms.add.workflow',
					'to' => $to
				), 
				array(
					'service' => 'queue.sms.send.workflow',
				));
				break;
			
			default :
				$memory['valid'] = false;
				$memory['msg'] = 'Invalid Type';
				$memory['status'] = 500;
				$memory['details'] = "Person alert device type : ".$type." is invalid @people.person.alert";
				return $memory;
				break;
		}

		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>