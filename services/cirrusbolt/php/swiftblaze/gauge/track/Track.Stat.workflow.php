<?php 
require_once(SBSERVICE);

/**
 *	@class TrackStatWorkflow
 *	@desc Returns statistics of reference
 *
 *	@param id long int Reference ID [memory]
 *
 *	@return stat array Statistics [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class TrackStatWorkflow implements Service {
	
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
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('chainid' => 'id'),
			'action' => 'info'
		),
		array(
			'service' => 'guard.chain.stat.workflow',
			'input' => array('chainid' => 'id')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('stat');
	}
	
}

?>