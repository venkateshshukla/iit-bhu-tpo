<?php 
require_once(SBSERVICE);

/**
 *	@class StudentInfoWorkflow
 *	@desc Returns student information by ID
 *
 *	@param stdid/id string Student ID [memory] optional default false
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string User name [memory] optional default ''
 *	@param batchid long int Portal ID [memory] optional default PORTAL_ID
 *
 *	@return student array Student information [memory]
 *	@return person array Person information [memory]
 *	@return contact array Student contact information [memory]
 *	@return personal array Student personal information [memory]
 *	@return stdid long int Student ID [memory]
 *	@return name string Student name [memory]
 *	@return title string Student title [memory]
 *	@return resume long int Student resume ID [memory]
 *	@return thumbnail long int Student thumbnail ID [memory]
 *	@return home long int Student notes ID [memory]
 *	@return dirid long int Thumbnail Directory ID [memory]
 *	@return username string Student username [memory]
 *	@return batchid long int Batch ID [memory]
 *	@return admin integer Is admin [memory]
 *	@return chain array Chain data [memory]
 *	@return batch array Batch information [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class StudentInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'stdid' => false, 'batchid' => STUDENT_PORTAL_ID, 'id' => 0),
			'set' => array('id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Student information given successfully';
		$attr = $memory['stdid'] ? 'stdid' : ($memory['id'] ? 'stdid' : 'owner');
		$memory['stdid'] = $memory['stdid'] ? $memory['stdid'] : ($memory['id'] ? $memory['id'] : $memory['keyid']);
		
		// args arguments
		$memory['auth'] = isset($memory['auth']) ? $memory['auth'] : true;
		
		$workflow = array(
		array(
			'service' => 'people.person.info.workflow',
			'input' => array('pnid' => 'stdid', 'peopleid' => 'batchid')
		),
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('stdid'),
			'conn' => 'exconn',
			'relation' => '`students`',
			'sqlcnd' => "where `$attr`='\${stdid}'",
			'errormsg' => 'Invalid Student ID',
			'successmsg' => 'Student information given successfully',
			'output' => array('entity' => 'student'),
			'cache' => false
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result', 'chain'),
			'params' => array('result.0' => 'student', 'result.0.stdid' => 'stdid',  'student.resume' => 'resume', 'student.home' => 'home', 'student.grade' => 'grade', 'student.slot' => 'slot', 'chain.parent' => 'batchid' /*'student.thumbnail' => 'thumbnail', 'student.username' => 'username'*/)
		),
		array(
			'service' => 'executive.batch.info.workflow'
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('student', 'person', 'contact', 'personal', 'stdid', 'resume', 'home', 'grade', 'slot', /* 'thumbnail', 'username',*/ 'dirid', 'batchid', 'admin', 'chain', 'batch');
	}
	
}

?>
