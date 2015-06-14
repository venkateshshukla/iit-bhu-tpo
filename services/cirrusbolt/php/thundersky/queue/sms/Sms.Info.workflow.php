<?php 
require_once(SBSERVICE);

/**
 *	@class SmsInfoWorkflow
 *	@desc Returns sms information by ID
 *
 *	@param smsid/id string SMS ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param queid long int Queue ID [memory] optional default 0
 *
 *	@return sms array SMS information [memory]
 *	@return queid long int Queue ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SmsInfoWorkflow implements Service {
	
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
		$memory['smsid'] = $memory['smsid'] ? $memory['smsid'] : $memory['id'];
		
		$service = array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'smsid', 'parent' => 'queid'),
			'conn' => 'cbqconn',
			'relation' => '`sms`',
			'sqlcnd' => "where `smsid`='\${id}'",
			'errormsg' => 'Invalid SMS ID',
			'successmsg' => 'SMS information given successfully',
			'output' => array('entity' => 'sms')
		);

		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('sms', 'queid', 'admin');
	}
	
}

?>
