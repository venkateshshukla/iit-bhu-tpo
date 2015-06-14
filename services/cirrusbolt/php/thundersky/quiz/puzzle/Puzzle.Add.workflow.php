<?php 
require_once(SBSERVICE);

/**
 *	@class PuzzleAddWorkflow
 *	@desc Adds new puzzle
 *
 *	@param statement string Puzzle statement [memory]
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
 *	@param level integer Web level [memory] optional default false (inherit quiz admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
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
class PuzzleAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'statement', 'shortans', 'longans', 'difficulty', 'explanation'),
			'optional' => array('quizid' => 0, 'qzname' => '', 'spoints' => 5, 'lpoints' => 10, 'level' => false, 'owner' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['verb'] = 'added';
		$memory['join'] = 'on';
		$memory['public'] = 1;
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.add.workflow',
			'args' => array('statement', 'shortans', 'longans', 'difficulty', 'spoints', 'lpoints', 'explanation'),
			'input' => array('parent' => 'quizid', 'cname' => 'statement', 'pname' => 'qzname'),
			'conn' => 'ayconn',
			'relation' => '`puzzles`',
			'type' => 'puzzle',
			'sqlcnd' => "(`pzlid`, `owner`, `statement`, `shortans`, `longans`, `difficulty`, `spoints`, `lpoints`, `explanation`) values (\${id}, \${owner}, '\${statement}', '\${shortans}', '\${longans}', \${difficulty}, \${spoints}, \${lpoints}, '\${explanation}')",
			'escparam' => array('statement', 'shortans', 'longans', 'explanation'),
			'successmsg' => 'Puzzle added successfully',
			'output' => array('id' => 'pzlid')
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'pzlid', 'parent' => 'quizid', 'cname' => 'name', 'qzname' => 'qzname'),
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