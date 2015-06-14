<?php 
require_once(SBSERVICE);
require_once(CBQUEUECONF);

/**
 *	@class PersonSendWorkflow
 *	@desc Sends verification key for person by ID
 *
 *	@param username string Username [memory]
 *	@param pnid long int Person ID [^memory] optional default false
 *	@param password string Password [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PersonSendWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('username'),
			'optional' => array('password' => 'to be reset to use your account')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Verification Sent Successfully';
		
		if($memory['password'] === false){
			$memory['password'] = 'not shown';
			$qry = "`pnid`=\${username}";
			$esc = array();
		}
		else {
			$qry = "`username`='\${username}' ";
			$esc = array('username');
		}
		
		//Snowblozm::$debug = true;
		$workflow = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('username'),
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'sqlcnd' => "where $qry and `device`<>''",
			'escparam' => $esc,
			'errormsg' => 'Invalid Username / Nothing to Verify'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0.pnid' => 'pnid', 'result.0.username' => 'username', 'result.0.owner' => 'keyid', 'result.0.device' => 'device', 'result.0.email' => 'email', 'result.0.phone' => 'phone')
		),
		array(
			'service' => 'cbcore.random.string.service',
			'length' => 15,
			'output' => array('random' => 'verify')
		),
		array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('pnid', 'verify'),
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'sqlcnd' => "set `verify`='\${verify}' where `pnid`=\${pnid}",
			'escparam' => array('verify'),
			'errormsg' => 'Invalid Person ID'
		),
		array(
			'service' => 'guard.chain.track.workflow',
			'input' => array('child' => 'pnid', 'cname' => 'username', 'user' => 'username'),
			'verb' => 'sent verification of',
			'join' => 'in',
			'public' => 0,
			'output' => array('id' => 'trackid')
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
		
		switch($memory['device']){
			case 'mail' :
				$workflow = array(
				array(
					'service' => 'cbcore.data.substitute.service',
					'args' => array('username', 'verify', 'email', 'password'),
					'data' => PERSON_SEND_MAIL_BODY,
					'output' => array('result' => 'body')
				),
				array(
					'service' => 'queue.mail.add.workflow',
					'to' => $memory['email'].PERSON_SEND_ADMIN_EMAIL,
					'input' => array('queid' => 'pnid', 'user' => 'username'),
					'subject' => PERSON_SEND_MAIL_SUBJECT
				), 
				array(
					'service' => 'queue.mail.send.workflow',
					'input' => array('queid' => 'pnid')
				));
				break;
			
			case 'sms' :
				$workflow = array(
				array(
					'service' => 'cbcore.data.substitute.service',
					'args' => array('username', 'verify', 'phone', 'password'),
					'data' => PERSON_SEND_SMS_BODY,
					'output' => array('result' => 'body')
				),
				array(
					'service' => 'queue.sms.add.workflow',
					'input' => array('queid' => 'pnid', 'user' => 'username'),
					'to' => $memory['phone'].PERSON_SEND_ADMIN_PHONE,
					'from' => PERSON_SEND_SMS_FROM
				), 
				array(
					'service' => 'queue.sms.send.workflow',
					'input' => array('queid' => 'pnid')
				));
				break;
			
			default :
				$memory['valid'] = false;
				$memory['msg'] = 'Invalid Type';
				$memory['status'] = 500;
				$memory['details'] = "Person verification type : ".$type." is invalid @people.person.send";
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