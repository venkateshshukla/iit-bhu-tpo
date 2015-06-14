<?php 
require_once(SBSERVICE);

/**
 *	@class ProblemAddWorkflow
 *	@desc Adds new problem
 *
 *	@param title string Title [memory]
 *	@param statement string Problem statement [memory]
 *	@param solution string Solution [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param quizid long int Quiz ID [memory] optional default 0
 *	@param qzname string Quiz Name [memory] optional default ''
 *	@param level integer Web level [memory] optional default false (inherit quiz admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
 *
 *	@return pbmid long int Problem ID [memory]
 *	@return quizid long int Quiz ID [memory]
 *	@return qzname string Quiz Name [memory]
 *	@return problem array Problem information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ProblemAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'title', 'statement', 'solution'),
			'optional' => array('quizid' => 0, 'qzname' => '', 'level' => false, 'owner' => false)
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
			'args' => array('title', 'statement', 'solution'),
			'input' => array('parent' => 'quizid', 'cname' => 'statement', 'pname' => 'qzname'),
			'conn' => 'ayconn',
			'relation' => '`problems`',
			'type' => 'problem',
			'sqlcnd' => "(`pbmid`, `owner`, `title`, `statement`, `solution`) values (\${id}, \${owner}, '\${title}', '\${statement}', '\${solution}')",
			'escparam' => array('title', 'statement', 'solution'),
			'successmsg' => 'Problem added successfully',
			'output' => array('id' => 'pbmid')
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'pbmid', 'parent' => 'quizid', 'cname' => 'name', 'qzname' => 'qzname'),
			'conn' => 'ayconn',
			'relation' => '`problems`',
			'sqlcnd' => "where `pbmid`=\${id}",
			'errormsg' => 'Invalid Problem ID',
			'type' => 'problem',
			'successmsg' => 'Problem information given successfully',
			'output' => array('entity' => 'problem'),
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
		return array('pbmid', 'quizid', 'qzname', 'problem', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>