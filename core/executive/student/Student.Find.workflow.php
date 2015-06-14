<?php 
require_once(SBSERVICE);

/**
 *	@class StudentFindWorkflow
 *	@desc Returns student information by user
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Student User [memory]
 *	@param name string Student Name [memory] optional default user
 *	@param batchid long int Portal ID [memory] optional default STUDENT_PORTAL_ID
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
 *	@return batchid long int Portal ID [memory]
 *	@return admin integer Is admin [memory]
 *	@return chain array Chain data [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class StudentFindWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('user', 'keyid'),
			'optional' => array('batchid' => STUDENT_PORTAL_ID, 'name' => false),
			'set' => array('name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Student information given successfully';
		$memory['name'] = $memory['name'] ? $memory['name'] : $memory['user'];
		
		$workflow = array(
		array(
			'service' => 'people.person.find.workflow'
		),
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('pnid'),
			'conn' => 'exconn',
			'relation' => '`students`',
			'sqlprj' => '`stdid`, `owner`, `username`, `name`, `email`, `rollno`, `specialization`, `category`, `resphone`, `resaddress`, `city`, `language`, `passport`, `father`, `foccupation`, `mother`, `moccupation`, `grade`, `resume`, `home`, `interests`, `remarks`, `points`, `ustatus`',
			'sqlcnd' => "where `stdid`=\${pnid}",
			'errormsg' => 'Invalid Student',
			'successmsg' => 'Student information given successfully'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result', 'chain'),
			'params' => array('result.0' => 'student', 'result.0.stdid' => 'stdid',  'student.resume' => 'resume', 'student.home' => 'home', 'student.grade' => 'gradeid', 'chain.parent' => 'batchid'  /*'student.thumbnail' => 'thumbnail', 'student.username' => 'username'*/)
		),
		array(
			'service' => 'executive.batch.info.workflow',
			'output' => array('admin' => 'btadmin')
		),
		array(
			'service' => 'executive.grade.info.workflow',
			'output' => array('admin' => 'grdadmin', 'chain' => 'grdchain')
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid']) return $memory;
		
		$memory['id'] = $memory['stdid'];
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('student', 'person', 'contact', 'personal', 'stdid', 'id', 'resume', 'home', 'grade', /*'thumbnail', 'username',*/ 'dirid', 'batchid', 'admin', 'chain', 'batch', 'btadmin');
	}
	
}

?>