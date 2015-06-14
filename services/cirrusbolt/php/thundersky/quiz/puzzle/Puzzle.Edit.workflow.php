<?php 
require_once(SBSERVICE);

/**
 *	@class PuzzleEditWorkflow
 *	@desc Edits puzzle using ID
 *
 *	@param pzlid long int Puzzle ID [memory]
 **	@param statement string Puzzle statement [memory]
 *	@param shortans string Short answer [memory]
 *	@param longans string Long answer [memory]
 *	@param difficulty integer Answer [memory] (1, 2, 3, 4, 5)
 *	@param spoints integer Short Points [memory] optional default 5
 *	@param lpoints integer Long Points [memory] optional default 10
 *	@param explanation string Explanation [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param quizid long int Quiz ID [memory] optional default 0
 *	@param qzname string Quiz Name [memory] optional default ''
 *
 *	@return pzlid long int Puzzle ID [memory]
 *	@return quizid long int Quiz ID [memory]
 *	@return qzname string Quiz Name [memory]
 *	@return puzzle array Puzzle information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PuzzleEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'pzlid', 'statement', 'shortans', 'longans', 'difficulty', 'spoints', 'lpoints', 'explanation', 'quizid', 'qzname')
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
			'args' => array('statement', 'shortans', 'longans', 'difficulty', 'spoints', 'lpoints', 'explanation'),
			'input' => array('id' => 'pzlid', 'cname' => 'statement', 'parent' => 'quizid', 'pname' => 'qzname'),
			'conn' => 'ayconn',
			'relation' => '`puzzles`',
			'type' => 'puzzle',
			'sqlcnd' => "set `statement`='\${statement}', `shortans`='\${shortans}', `longans`='\${longans}', `difficulty`=\${difficulty}, `spoints`=\${spoints}, `lpoints`=\${lpoints}, `explanation`='\${explanation}' where `pzlid`=\${id}",
			'escparam' => array('statement', 'shortans', 'longans', 'explanation'),
			'check' => false,
			'successmsg' => 'Puzzle edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'pzlid', 'parent' => 'quizid', 'cname' => 'name', 'pname' => 'qzname'),
			'conn' => 'ayconn',
			'relation' => '`puzzles`',
			'sqlcnd' => "where `pzlid`=\${id}",
			'errormsg' => 'Invalid Puzzle ID',
			'type' => 'puzzle',
			'successmsg' => 'Puzzle information given successfully',
			'output' => array('entity' => 'puzzle'),
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
		return array('pzlid', 'quizid', 'qzname', 'puzzle', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>