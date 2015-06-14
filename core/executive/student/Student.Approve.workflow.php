<?php 
require_once(SBSERVICE);

/**
 *	@class StudentApproveWorkflow
 *	@desc Approves student by ID
 *
 *	@param stdid long int Student ID [memory]
 *	@param batchid long int Portal ID [memory] optional default STUDENT_PORTAL_ID
 *	@param keyid long int Usage Key ID [memory]
 *	@param suspend boolean Is suspend [memory] optional default false
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class StudentApproveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'stdid'),
			'optional' => array('batchid' => STUDENT_PORTAL_ID, 'btname' => '', 'suspend' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$verb = $memory['suspend'] ? 'Suspended' : 'Approved';
		$memory['msg'] = "Student $verb Successfully".($memory['suspend'] ? '' : '. Please Edit Student Type.');
		$state = $memory['suspend'] ? '0' : 'A';
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'batchid'),
			'action' => 'edit'
		),
		array(
			'service' => 'executive.student.info.workflow'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('student'),
			'params' => array('student.owner' => 'owner')
		),
		array(
			'service' => 'guard.web.set.workflow',
			'state' => $state
		),
		array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('owner'),
			'conn' => 'exconn',
			'relation' => '`students`',
			'sqlcnd' => "set `ustatus`='$state' where `owner`=\${owner}",
			'check' => false,
			'errormsg' => 'Invalid Key ID'
		),
		array(
			'service' => 'executive.student.info.workflow'
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'batchid'),
			'output' => array('chain' => 'pchain')
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
			
		$memory['padmin'] = $memory['admin'];
		$memory['admin'] = 1;
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('stdid', 'batchid', 'btname', 'student', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>