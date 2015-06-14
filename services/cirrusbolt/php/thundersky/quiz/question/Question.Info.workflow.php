<?php 
require_once(SBSERVICE);

/**
 *	@class QuestionInfoWorkflow
 *	@desc Returns question information by ID
 *
 *	@param qstid/id long int Question ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param user string Key User [memory]
 *	@param quizid long int Quiz ID [memory] optional default 0
 *	@param qzname/name string Quiz name [memory] optional default ''
 *
 *	@return question array Question information [memory]
 *	@return qzname string Quiz name [memory]
 *	@return quizid long int Quiz ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class QuestionInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('qstid'),
			'optional' => array('keyid' => false, 'user' => '', 'qzname' => false, 'name' => '', 'quizid' => false, 'id' => 0),
			'set' => array('id', 'name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['qstid'] = $memory['qstid'] ? $memory['qstid'] : $memory['id'];
		
		$service = array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'qstid', 'parent' => 'quizid', 'cname' => 'name', 'pname' => 'qzname'),
			'conn' => 'ayconn',
			'relation' => '`questions`',
			'sqlcnd' => "where `qstid`=\${id}",
			'errormsg' => 'Invalid Question ID',
			'type' => 'question',
			'successmsg' => 'Question information given successfully',
			'output' => array('entity' => 'question')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('question', 'qzname', 'quizid', 'admin');
	}
	
}

?>