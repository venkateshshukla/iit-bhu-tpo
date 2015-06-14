<?php 
require_once(SBSERVICE);

/**
 *	@class MailRemoveWorkflow
 *	@desc Removes mail by ID
 *
 *	@param maillid long int Mail ID [memory]
 *	@param queid long int Queue ID [memory] optional default 0
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class MailRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'mailid'),
			'optional' => array('queid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.remove.workflow',
			'input' => array('id' => 'mailid', 'parent' => 'queid'),
			'conn' => 'cbqconn',
			'relation' => '`mails`',
			'sqlcnd' => "where `mailid`=\${id} and `state`=0",
			'errormsg' => 'Mail Already Sent / Invalid Mail ID',
			'successmsg' => 'Mail removed successfully'
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