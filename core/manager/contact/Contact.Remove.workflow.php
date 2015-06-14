<?php 
require_once(SBSERVICE);

/**
 *	@class ContactRemoveWorkflow
 *	@desc Removes contact by ID
 *
 *	@param cntid long int Contact ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param mngrid long int Manager ID [memory] optional default MANAGER_ID
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ContactRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'cntid'),
			'optional' => array('mngrid' => MANAGER_ID)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.remove.workflow',
			'input' => array('id' => 'cntid', 'parent' => 'mngrid'),
			'conn' => 'exconn',
			'relation' => '`contacts`',
			'type' => 'contact',
			'sqlcnd' => "where `cntid`=\${id}",
			'errormsg' => 'Invalid Contact ID',
			'successmsg' => 'Contact removed successfully'
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