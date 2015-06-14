<?php 
require_once(SBSERVICE);
require_once(CBQUEUECONF);

/**
 *	@class PersonResetWorkflow
 *	@desc Resets key for person by ID
 *
 *	@param user string Person username [memory]
 *	@param email string Person email [memory]
 *	@param context string Context [constant as CONTEXT]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PersonResetWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('user', 'email')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Password changed successfully. Check your inbox for new password.';
		
		$workflow = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('user', 'email'),
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'sqlcnd' => "where `username`='\${user}' and `email`='\${email}' and `device`=''",
			'escparam' => array('user', 'email'),
			'errormsg' => 'Invalid Username / Email'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0.owner' => 'keyid', 'result.0.username' => 'username', 'result.0.pnid' => 'pnid')
		),
		array(
			'service' => 'cbcore.random.string.service',
			'length' => 12,
			'output' => array('random' => 'password')
		),
		array(
			'service' => 'guard.key.edit.workflow',
			'input' => array('key' => 'password')
		),
		array(
			'service' => 'cbcore.data.substitute.service',
			'args' => array('username', 'password', 'email'),
			'data' => PERSON_RESET_MAIL_BODY,
			'output' => array('result' => 'body')
		),
		array(
			'service' => 'queue.mail.add.workflow',
			'input' => array('queid' => 'pnid', 'to' => 'email', 'user' => 'username'),
			'subject' => PERSON_RESET_MAIL_SUBJECT
		),
		array(
			'service' => 'queue.mail.send.workflow',
			'input' => array('queid' => 'pnid')
		),
		array(
			'service' => 'guard.chain.track.workflow',
			'input' => array('child' => 'pnid', 'cname' => 'user'),
			'verb' => 'reset',
			'join' => 'in',
			'public' => 0,
			'output' => array('id' => 'trackid')
		));
		
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