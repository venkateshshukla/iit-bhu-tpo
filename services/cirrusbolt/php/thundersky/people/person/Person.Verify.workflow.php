<?php 
require_once(SBSERVICE);

/**
 *	@class PersonVerifyWorkflow
 *	@desc Changes key for person by ID
 *
 *	@param username string Person username [memory]
 *	@param verify string Verification code [memory]
 *	@param context string Context [constant as CONTEXT]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PersonVerifyWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('username', 'verify')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		if($memory['verify'] == ''){
			$memory['valid'] = false;
			$memory['msg'] = 'Invalid verification code';
			$memory['status'] = 500;
			$memory['details'] = "Person verification code : ".$memory['verify']." is invalid @people.person.verify";
			return $memory;
		}
		
		$memory['msg'] = 'Verified successfully';
		//$attr = $memory['phone'] ? 'phone' : 'email';
		//$memory['phone'] = $memory['phone'] ? $memory['phone'] : $memory['email'];
		
		$workflow = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('username', 'verify'),
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'sqlcnd' => "where `username`='\${username}' and `verify`='\${verify}'",
			'escparam' => array('username', 'verify'),
			'errormsg' => 'Invalid Verification Code'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0.owner' => 'owner', 'result.0.pnid' => 'pnid')
		),
		array(
			'service' => 'guard.key.verify.workflow',
			'input' => array('keyid' => 'owner'),
			'context' => CONTEXT
		),
		array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('owner'),
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'sqlcnd' => "set `verify`='', `device`='' where `owner`=\${owner}",
			'errormsg' => 'Invalid Person'
		),
		array(
			'service' => 'guard.chain.track.workflow',
			'input' => array('child' => 'pnid', 'cname' => 'username', 'keyid' => 'owner', 'user' => 'username'),
			'verb' => 'verified account of',
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