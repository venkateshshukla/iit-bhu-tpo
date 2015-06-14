<?php 
require_once(SBSERVICE);

/**
 *	@class SlotFindWorkflow
 *	@desc Returns slot information by name
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
class SlotFindWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('keyid' => false, 'btname' => false, 'user' => '', 'btname' => false, 'name' => '', 'batchid' => STUDENT_PORTAL_ID),
			'set' => array('name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['btname'] = $memory['btname'] ? $memory['btname'] : $memory['name'];
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.find.workflow',
			'input' => array('parent' => 'batchid', 'cname' => 'name', 'pname' => 'btname'),
			'args' => array('btname'),
			'conn' => 'exconn',
			'idkey' => 'slotid',
			'relation' => '`slots`',
			'sqlcnd' => "where `btname`='\${btname}'",
			'errormsg' => 'Invalid Slot Name',
			'escparam' => array('btname'),
			'type' => 'slot',
			'successmsg' => 'Slot information given successfully',
			'output' => array('entity' => 'slot', 'id' => 'slotid')
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('slot'),
			'params' => array('slot.slotid' => 'slotid')
		),
		array(
			'service' => 'executive.student.list.workflow',
			'output' => array('admin' => 'stdadmin', 'padmin' => 'btadmin'),
			'padmin' => false
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
		
		$memory['id'] = $memory['slotid'];
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('slot', 'id', 'slotid', 'btname', 'batchid', 'admin', 'btname', 'students', 'id', 'slotid', 'btname', 'stdadmin', 'chain', 'total', 'pgno', 'pgsz');
	}
	
}

?>