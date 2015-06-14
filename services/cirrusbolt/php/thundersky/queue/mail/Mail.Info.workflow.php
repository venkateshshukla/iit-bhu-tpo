<?php 
require_once(SBSERVICE);

/**
 *	@class MailInfoWorkflow
 *	@desc Returns mail information by ID
 *
 *	@param mailid/id string Mail ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param queid long int Queue ID [memory] optional default 0
 *
 *	@return mail array Mail information [memory]
 *	@return queid long int Queue ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class MailInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('queid' => 0, 'mailid' => false, 'id' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['mailid'] = $memory['mailid'] ? $memory['mailid'] : $memory['id'];
		
		$service = array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'mailid', 'parent' => 'queid'),
			'conn' => 'cbqconn',
			'relation' => '`mails`',
			'sqlcnd' => "where `mailid`='\${id}'",
			'errormsg' => 'Invalid Mail ID',
			'successmsg' => 'Mail information given successfully',
			'output' => array('entity' => 'mail')
		);
		
		$memory = Snowblozm::run($service, $memory);
		if($memory['valid'])
			$memory['mail']['attach'] = json_decode($memory['mail']['attach']);
		
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('mail', 'queid', 'admin');
	}
	
}

?>
