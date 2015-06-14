<?php 
require_once(SBSERVICE);

/**
 *	@class StudentUpdateWorkflow
 *	@desc Updates student using ID
 *
 *	@param stdid long int Student ID [memory]
 *	@param rollno string Roll no [memory]
 *	@param interests string Interests [memory]
 *	@param ustatus string UStatus [memory]
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Username [memory]
 *	@param batchid long int Batch ID [memory] optional default 0
 *	@param btname string Batch Name [memory] optional default ''
 *
 *	@return stdid long int Student ID [memory]
 *	@return batchid long int Batch ID [memory]
 *	@return btname string Batch Name [memory]
 *	@return student array Student information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class StudentUpdateWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'stdid', 'rollno', 'interests', 'remarks', 'ustatus', 'batchid', 'btname')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'stdid'),
			'aistate' => false,
			'init' => false
		),
		array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('stdid', 'rollno', 'interests', 'remarks', 'ustatus'),
			'conn' => 'exconn',
			'relation' => '`students`',
			'sqlcnd' => "set `rollno`='\${rollno}', `interests`='\${interests}', `remarks`='\${remarks}', `ustatus`='\${ustatus}' where `stdid`=\${stdid}",
			'successmsg' => 'Student updated successfully',
			'check' => false,
			'escparam' => array('rollno', 'interests', 'remarks', 'ustatus'),
			'errormsg' => 'No Change / Invalid Student ID'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'stdid', 'parent' => 'batchid', 'cname' => 'name', 'pname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`students`',
			'sqlprj' => '`stdid`, `username`, `name`, `email`, `rollno`, `resume`, `home`, `interests`, `remarks`, `ustatus`',
			'sqlcnd' => "where `stdid`=\${id}",
			'errormsg' => 'Invalid Student ID',
			'type' => 'person',
			'successmsg' => 'Student information given successfully',
			'output' => array('entity' => 'student'),
			'auth' => false,
			'track' => false,
			'sinit' => false
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