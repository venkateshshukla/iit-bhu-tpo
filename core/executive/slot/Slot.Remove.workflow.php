<?php 
require_once(SBSERVICE);

/**
 *	@class SlotRemoveWorkflow
 *	@desc Removes slot by ID
 *
 *	@param slotid long int Slot ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 
 *	@param batchid long int Batch ID [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SlotRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'slotid'),
			'optional' => array('batchid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.remove.workflow',
			'input' => array('id' => 'slotid', 'parent' => 'batchid'),
			'conn' => 'exconn',
			'relation' => '`slots`',
			'type' => 'slot',
			'sqlcnd' => "where `slotid`=\${id}",
			'errormsg' => 'Invalid Slot ID',
			'successmsg' => 'Slot removed successfully'
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