<?php 
require_once(SBSERVICE);

/**
 *	@class QuestionEditWorkflow
 *	@desc Edits question using ID
 *
 *	@param qstid long int Question ID [memory]
 *	@param statement string Question statement [memory]
 *	@param option1 string Option 1 [memory]
 *	@param option2 string Option 2 [memory]
 *	@param option3 string Option 3 [memory]
 *	@param option4 string Option 4 [memory]
 *	@param answer integer Answer [memory] (1, 2, 3, 4)
 *	@param explanation string Explanation [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param quizid long int Quiz ID [memory] optional default 0
 *	@param qzname string Quiz Name [memory] optional default ''
 *
 *	@return qstid long int Question ID [memory]
 *	@return quizid long int Quiz ID [memory]
 *	@return qzname string Quiz Name [memory]
 *	@return question array Question information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class QuestionEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'qstid', 'statement', 'option1', 'option2', 'option3', 'option4', 'answer', 'explanation', 'quizid', 'qzname')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){		
		$memory['public'] = 1;
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('statement', 'option1', 'option2', 'option3', 'option4', 'answer', 'explanation'),
			'input' => array('id' => 'qstid', 'cname' => 'statement', 'parent' => 'quizid', 'pname' => 'qzname'),
			'conn' => 'ayconn',
			'relation' => '`questions`',
			'type' => 'question',
			'sqlcnd' => "set `statement`='\${statement}', `option1`='\${option1}', `option2`='\${option2}', `option3`='\${option3}', `option4`='\${option4}', `answer`='\${answer}', `explanation`='\${explanation}' where `qstid`=\${id}",
			'escparam' => array('statement', 'option1', 'option2', 'option3', 'option4', 'explanation'),
			'check' => false,
			'successmsg' => 'Question edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'qstid', 'parent' => 'quizid', 'cname' => 'name', 'pname' => 'qzname'),
			'conn' => 'ayconn',
			'relation' => '`questions`',
			'sqlcnd' => "where `qstid`=\${id}",
			'errormsg' => 'Invalid Question ID',
			'type' => 'question',
			'successmsg' => 'Question information given successfully',
			'output' => array('entity' => 'question'),
			'auth' => false,
			'track' => false,
			'sinit' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'quizid'),
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
		return array('qstid', 'quizid', 'qzname', 'question', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>