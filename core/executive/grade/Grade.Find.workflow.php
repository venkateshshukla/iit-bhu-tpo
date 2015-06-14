<?php 
require_once(SBSERVICE);

/**
 *	@class GradeFindWorkflow
 *	@desc Returns grade information by name
 *
 *	@param gradeid/id long int Grade ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param user string Key User [memory]
 *	@param batchid long int Batch ID [memory] optional default 0
 *	@param btname/name string Batch name [memory] optional default ''
 *
 *	@return grade array Grade information [memory]
 *	@return btname string Batch name [memory]
 *	@return batchid long int Batch ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class GradeFindWorkflow implements Service {
	
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
			'idkey' => 'gradeid',
			'relation' => '`gradees`',
			'sqlcnd' => "where `btname`='\${btname}'",
			'errormsg' => 'Invalid Grade Name',
			'escparam' => array('btname'),
			'type' => 'grade',
			'successmsg' => 'Grade information given successfully',
			'output' => array('entity' => 'grade', 'id' => 'gradeid')
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('grade'),
			'params' => array('grade.gradeid' => 'gradeid')
		),
		array(
			'service' => 'executive.student.list.workflow',
			'output' => array('admin' => 'stdadmin', 'padmin' => 'btadmin'),
			'padmin' => false
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
		
		$memory['id'] = $memory['gradeid'];
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('grade', 'id', 'gradeid', 'btname', 'batchid', 'admin', 'btname', 'students', 'id', 'gradeid', 'btname', 'stdadmin', 'chain', 'total', 'pgno', 'pgsz');
	}
	
}

?>