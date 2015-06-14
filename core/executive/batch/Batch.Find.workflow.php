<?php 
require_once(SBSERVICE);

/**
 *	@class BatchFindWorkflow
 *	@desc Returns batch information by name
 *
 *	@param batchid/id long int Batch ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param user string Key User [memory]
 *	@param portalid long int Portal ID [memory] optional default 0
 *	@param plname/name string Portal name [memory] optional default ''
 *
 *	@return batch array Batch information [memory]
 *	@return plname string Portal name [memory]
 *	@return portalid long int Portal ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class BatchFindWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('keyid' => false, 'btname' => false, 'user' => '', 'plname' => false, 'name' => '', 'portalid' => STUDENT_PORTAL_ID, 'export' => false, 'archive' => false),
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
			'input' => array('parent' => 'portalid', 'cname' => 'name', 'pname' => 'plname'),
			'args' => array('btname'),
			'conn' => 'exconn',
			'idkey' => 'batchid',
			'relation' => '`batches`',
			'sqlcnd' => "where `btname`='\${btname}'",
			'errormsg' => 'Invalid Batch Name',
			'escparam' => array('btname'),
			'type' => 'batch',
			'successmsg' => 'Batch information given successfully',
			'output' => array('entity' => 'batch', 'id' => 'batchid')
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('batch'),
			'params' => array('batch.batchid' => 'batchid', 'batch.resumes' => 'resumes')
		),
		array(
			'service' => 'executive.student.list.workflow',
			'output' => array('admin' => 'stdadmin', 'padmin' => 'btadmin'),
			'padmin' => false
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
		
		$memory['id'] = $memory['batchid'];
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('batch', 'id', 'batchid', 'plname', 'portalid', 'admin', 'btname', 'resumes', 'students', 'id', 'batchid', 'btname', 'stdadmin', 'chain', 'total', 'pgno', 'pgsz');
	}
	
}

?>