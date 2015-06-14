<?php 
require_once(SBSERVICE);

/**
 *	@class QuestionRemoveWorkflow
 *	@desc Removes question by ID
 *
 *	@param qstid long int Question ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param quizid long int Quiz ID [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class QuestionRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'qstid'),
			'optional' => array('quizid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.remove.workflow',
			'input' => array('id' => 'qstid', 'parent' => 'quizid'),
			'conn' => 'ayconn',
			'relation' => '`questions`',
			'type' => 'question',
			'sqlcnd' => "where `qstid`=\${id}",
			'errormsg' => 'Invalid Question ID',
			'successmsg' => 'Question removed successfully'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>