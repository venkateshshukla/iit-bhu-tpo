<?php 
require_once(SBSERVICE);

/**
 *	@class SlotInfoWorkflow
 *	@desc Returns slot information by ID
 *
 *	@param slotid/id long int Slot ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param user string Key User [memory]
 *	@param batchid long int Batch ID [memory] optional default 0
 *	@param btname/name string Batch name [memory] optional default ''
 *
 *	@return slot array Slot information [memory]
 *	@return btname string Batch name [memory]
 *	@return batchid long int Batch ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SlotInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('slotid'),
			'optional' => array('keyid' => false, 'user' => '', 'btname' => false, 'name' => '', 'batchid' => STUDENT_PORTAL_ID, 'id' => 0),
			'set' => array('id', 'name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['slotid'] = $memory['slotid'] ? $memory['slotid'] : $memory['id'];
		
		$service = array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'slotid', 'parent' => 'batchid', 'cname' => 'name', 'pname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`slots`',
			'sqlcnd' => "where `slotid`=\${id}",
			'errormsg' => 'Invalid Slot ID',
			'type' => 'slot',
			'successmsg' => 'Slot information given successfully',
			'output' => array('entity' => 'slot')
		);
		
		$memory = Snowblozm::run($service, $memory);
		if(!$memory['valid']){
			if($memory['status'] != 403)
				return $memory;
			$memory['valid'] = true;
			$memory['slot'] = false;
			$memory['admin'] = 0;
			$memory['chain'] = false;
		}
		
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('slot', 'btname', 'batchid', 'admin', 'chain');
	}
	
}

?>