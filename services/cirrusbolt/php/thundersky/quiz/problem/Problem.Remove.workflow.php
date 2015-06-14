<?php 
require_once(SBSERVICE);

/**
 *	@class ProblemRemoveWorkflow
 *	@desc Removes problem by ID
 *
 *	@param pbmid long int Problem ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param quizid long int Quiz ID [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ProblemRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'pbmid'),
			'optional' => array('quizid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.remove.workflow',
			'input' => array('id' => 'pbmid', 'parent' => 'quizid'),
			'conn' => 'ayconn',
			'relation' => '`problems`',
			'type' => 'problem',
			'sqlcnd' => "where `pbmid`=\${id}",
			'errormsg' => 'Invalid Problem ID',
			'successmsg' => 'Problem removed successfully'
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