<?php 
require_once(SBSERVICE);

/**
 *	@class SessionRemoveWorkflow
 *	@desc Removes session by ID
 *
 *	@param sessionid string Session ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SessionRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('sessionid')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Session removed successfully';
		
		$service = array(
			'service' => 'transpera.relation.delete.workflow',
			'args' => array('sessionid'),
			'conn' => 'cbconn',
			'relation' => '`sessions`',
			'sqlcnd' => "where `sessionid`='\${sessionid}'",
			'escparam' => array('sessionid'),
			'errormsg' => 'Invalid Session ID'
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