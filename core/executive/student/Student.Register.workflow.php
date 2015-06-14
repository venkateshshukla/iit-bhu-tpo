<?php 
require_once(SBSERVICE);

/**
 *	@class StudentRegisterWorkflow
 *	@desc Registers new student
 *
 *	@param name string Student name [memory]
 *	@param username string Student username [memory]
 *	@param email string Email [memory]
 *	@param rollno string Roll no [memory]
 *
 *	@param keyid long int Usage Key [memory]
 *	@param batchid long int Batch ID [memory] optional default STUDENT_PORTAL_ID
 *	@param btname string Batch Name [memory] optional default ''
 *	@param level integer Web level [memory] optional default 1 (batch admin access allowed)
 *
 *	@return stdid long int Student ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class StudentRegisterWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('user', 'rollno', 'dept', 'course', 'year'),
			'optional' => array('level' => 2, 'keyid' => TPO_KEY)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Student created successfully. Registration verification pending.';
		
		$memory['verb'] = 'registered';
		$memory['join'] = 'on';
		$memory['public'] = 1;
		
		if($memory['user'] == ''){
			$memory['valid'] = true;
			$memory['msg'] = 'Please login with your Google Account.';
			$memory['status'] = 500;
			$memory['details'] = 'Invalid user @student.register';
			return $memory;
		}
		
		if(strpos($memory['user'], '@') === false){
			$memory['valid'] = true;
			$memory['msg'] = 'Please relogin with your Google Account.';
			$memory['status'] = 500;
			$memory['details'] = 'Invalid user @student.register';
			return $memory;
		}
		
		$memory['email'] = $memory['user'];
		$parts = explode('@', $memory['email']);
		$uparts = explode('.', $parts[0]);
		$memory['name'] = ucfirst($uparts[0]).' '.(isset($uparts[1]) ? ucfirst($uparts[1]) : '');
		
		$memory['btname'] = $memory['course'].'.';
		if($parts[1] == 'itbhu.ac.in' && isset($uparts[2])){
			$memory['btname'] .= $uparts[2];
			$memory['username'] = $parts[0];
		}
		else {
			$year = $memory['year'];
			switch($memory['course']){
				case 'btech' :
					$year -= 4;
					break;
				case 'idd' :
				case 'imd' :
					$year -= 5;
					break;
				case 'mtech' :
					$year -= 2;
					break;
				default :
					break;
			}
			
			$year %= 100;
			$year = (($year == 0) ? '00' : ($year < 10 ? '0'.$year : $year));
			$memory['btname'] .= $memory['dept'].$year;
			$memory['username'] = $parts[0].'.'.$memory['dept'].$year;
		}
		
		$memory['course'] = $memory['course'] == 'imd' ? 'idd' : $memory['course'];
		
		$memory = Snowblozm::run(array(
			'service' => 'executive.batch.find.workflow'
		), $memory);
		
		if(!$memory['valid']){
			$memory = Snowblozm::run(array(
				'service' => 'executive.batch.add.workflow',
				'level' => 1
			), $memory);
			
			if(!$memory['valid'])
				return $memory;
		}
		
		$memory['level'] = 2;
		$workflow = array(
		array(
			'service' => 'executive.student.add.workflow'
		),
		array(
			'service' => 'invoke.interface.session.workflow',
			'user' => false, 
			'password' => false
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
			
		if($parts[1] != 'itbhu.ac.in'){
			$msg = $memory['msg'];
			$memory = Snowblozm::execute(array(
			array(
				'service' => 'cbcore.data.select.service',
				'args' => array('student'),
				'params' => array('student.owner' => 'owner')
			),
			array(
				'service' => 'transpera.relation.update.workflow',
				'args' => array('owner'),
				'conn' => 'exconn',
				'relation' => '`students`',
				'sqlcnd' => "set `ustatus`='0' where `owner`=\${owner}",
				'check' => false,
				'errormsg' => 'Invalid Key ID'
			),
			array(
				'service' => 'guard.web.set.workflow'
			)), $memory);
			
			if(!$memory['valid'])
				return $memory;
			
			$memory['student']['ustatus'] = '0';
			$memory['msg'] = $msg.' Your account needs approval to get activated. Please contact your TPR.';
		}
		
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('stdid', 'portalid', 'plname', 'student', 'chain', 'pchain', 'admin', 'padmin', 'batch');
	}
	
}

?>