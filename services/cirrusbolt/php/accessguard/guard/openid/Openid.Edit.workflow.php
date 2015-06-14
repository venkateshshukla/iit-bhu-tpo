<?php 
require_once(SBSERVICE);

/**
 *	@class OpenidEditWorkflow
 *	@desc Edits openid key of Chain
 *
 *	@param email string Email ID [memory]
 *	@param keyid long int Key ID [memory]
 *	@param oid long int Openid ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class OpenidEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'oid', 'email')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Openid Email Edited Successfully';
		
		$service = array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('keyid', 'email', 'oid'),
			'conn' => 'cbconn',
			'relation' => '`openids`',
			'sqlcnd' => "set `email`='\${email}' where `oid`=\${oid} and `keyid`=\${keyid}",
			'escparam' => array('email'),
			'check' => false,
			'errormsg' => 'Invalid Openid ID'
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