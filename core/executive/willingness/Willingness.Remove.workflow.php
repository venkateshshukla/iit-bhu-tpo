<?php 
require_once(SBSERVICE);

/**
 *	@class WillingnessRemoveWorkflow
 *	@desc Removes willingness by ID
 *
 *	@param wlgsid long int Willingness ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param batchid long int Batch ID [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class WillingnessRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'wlgsid'),
			'optional' => array('batchid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.remove.workflow',
			'input' => array('id' => 'wlgsid', 'parent' => 'batchid'),
			'conn' => 'exconn',
			'relation' => '`willingnesses`',
			'type' => 'willingness',
			'sqlcnd' => "where `wlgsid`=\${id}",
			'errormsg' => 'Invalid Willingness ID',
			'successmsg' => 'Willingness removed successfully'
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