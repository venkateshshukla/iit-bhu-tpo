<?php 
require_once(SBSERVICE);

/** TODO
 *	@class StudentRemoveWorkflow
 *	@desc Removes student by ID
 *
 *	@param stdid long int Student ID [memory]
 *	@param batchid long int Portal ID [memory] optional default STUDENT_PORTAL_ID
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class StudentRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'stdid'),
			'optional' => array('batchid' => STUDENT_PORTAL_ID)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Student removed successfully';
		
		$workflow = array(
		array(
			'service' => 'executive.batch.info.workflow'
		),
		array(
			'service' => 'executive.student.info.workflow'
		),
		array(
			'service' => 'executive.student.approve.workflow'
		),
		array(
			'service' => 'people.person.remove.workflow',
			'input' => array('pnid' => 'stdid', 'peopleid' => 'batchid')
		),
		array(
			'service' => 'transpera.relation.delete.workflow',
			'args' => array('stdid'),
			'conn' => 'exconn',
			'relation' => '`students`',
			'sqlcnd' => "where `stdid`=\${stdid}",
			'errormsg' => 'Invalid Student ID'
		),
		array(
			'service' => 'display.board.remove.workflow',
			'input' => array('boardid' => 'home', 'forumid' => 'notes')
		),
		array(
			'service' => 'executive.grade.remove.workflow',
			'input' => array('gradeid' => 'grade')
		),
		array(
			'service' => 'executive.slot.remove.workflow',
			'input' => array('slotid' => 'slot')
		),
		array(
			'service' => 'storage.file.remove.workflow',
			'input' => array('fileid' => 'resume', 'dirid' => 'resumes')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>