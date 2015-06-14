<?php 
require_once(SBSERVICE);

/**
 *	@class TrackWriteWorkflow
 *	@desc Tracks writes of reference
 *
 *	@param id long int Reference ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class TrackWriteWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('id')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'guard.chain.write.workflow',
			'input' => array('chainid' => 'id')
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