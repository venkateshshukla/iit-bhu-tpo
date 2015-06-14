<?php 
require_once(SBSERVICE);

/**
 *	@class ContactInfoWorkflow
 *	@desc Returns contact information by ID
 *
 *	@param cntid/id long int Contact ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param user string Key User [memory]
 *	@param mngrid long int Manager ID [memory] optional default MANAGER_ID
 *	@param mngrname/name string Manager name [memory] optional default ''
 *
 *	@return contact array Contact information [memory]
 *	@return mngrname string Manager name [memory]
 *	@return mngrid long int Manager ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ContactInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('cntid'),
			'optional' => array('keyid' => false, 'user' => '', 'mngrname' => false, 'name' => '', 'mngrid' => false, 'id' => MANAGER_ID),
			'set' => array('id', 'name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['cntid'] = $memory['cntid'] ? $memory['cntid'] : $memory['id'];
		
		$service = array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'cntid', 'parent' => 'mngrid', 'cname' => 'name', 'pname' => 'mngrname'),
			'conn' => 'exconn',
			'relation' => '`contacts`',
			'sqlcnd' => "where `cntid`=\${id}",
			'errormsg' => 'Invalid Contact ID',
			'type' => 'contact',
			'successmsg' => 'Contact information given successfully',
			'output' => array('entity' => 'contact')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('contact', 'mngrname', 'mngrid', 'admin');
	}
	
}

?>