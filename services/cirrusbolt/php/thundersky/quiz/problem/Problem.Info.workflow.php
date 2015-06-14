<?php 
require_once(SBSERVICE);

/**
 *	@class ProblemInfoWorkflow
 *	@desc Returns problem information by ID
 *
 *	@param pbmid/id long int Problem ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param user string Key User [memory]
 *	@param quizid long int Quiz ID [memory] optional default 0
 *	@param qzname/name string Quiz name [memory] optional default ''
 *
 *	@return problem array Problem information [memory]
 *	@return qzname string Quiz name [memory]
 *	@return quizid long int Quiz ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ProblemInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('pbmid'),
			'optional' => array('keyid' => false, 'user' => '', 'qzname' => false, 'name' => '', 'quizid' => false, 'id' => 0),
			'set' => array('id', 'name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['pbmid'] = $memory['pbmid'] ? $memory['pbmid'] : $memory['id'];
		
		$service = array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'pbmid', 'parent' => 'quizid', 'cname' => 'name', 'pname' => 'qzname'),
			'conn' => 'ayconn',
			'relation' => '`problems`',
			'sqlcnd' => "where `pbmid`=\${id}",
			'errormsg' => 'Invalid Problem ID',
			'type' => 'problem',
			'successmsg' => 'Problem information given successfully',
			'output' => array('entity' => 'problem')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('problem', 'qzname', 'quizid', 'admin');
	}
	
}

?>