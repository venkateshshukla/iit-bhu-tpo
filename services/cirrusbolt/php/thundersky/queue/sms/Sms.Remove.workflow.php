<?php 
require_once(SBSERVICE);

/**
 *	@class SmsRemoveWorkflow
 *	@desc Removes sms by ID
 *
 *	@param smslid long int SMS ID [memory]
 *	@param queid long int Queue ID [memory] optional default 0
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SmsRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'smsid'),
			'optional' => array('queid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.remove.workflow',
			'input' => array('id' => 'smsid', 'parent' => 'queid'),
			'conn' => 'cbqconn',
			'relation' => '`sms`',
			'sqlcnd' => "where `smsid`=\${id} and `state`=0",
			'errormsg' => 'SMS Already Sent / Invalid SMS ID',
			'successmsg' => 'SMS removed successfully'
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