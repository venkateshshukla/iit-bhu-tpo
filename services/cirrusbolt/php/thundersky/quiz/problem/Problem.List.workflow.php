<?php 
require_once(SBSERVICE);

/**
 *	@class ProblemListWorkflow
 *	@desc Returns all problems information in quiz
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param quizid/id long int Quiz ID [memory] optional default 0
 *	@param qzname/name string Quiz name [memory] optional default ''
 *
 *	@param pgsz long int Paging Size [memory] optional default 25
 *	@param pgno long int Paging Index [memory] optional default 0
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return problems array Problems information [memory]
 *	@return quizid long int Quiz ID [memory]
 *	@return qzname string Quiz Name [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return pgsz long int Paging Size [memory]
 *	@return pgno long int Paging Index [memory]
 *	@return total long int Paging Total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ProblemListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'quizid' => false, 'id' => 0, 'qzname' => false, 'name' => '', 'pgsz' => 25, 'pgno' => 0, 'total' => false, 'padmin' => true),
			'set' => array('id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['quizid'] = $memory['quizid'] ? $memory['quizid'] : $memory['id'];
		$memory['qzname'] = $memory['qzname'] ? $memory['qzname'] : $memory['name'];
		
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'quizid', 'pname' => 'qzname'),
			'conn' => 'ayconn',
			'relation' => '`problems`',
			'type' => 'problem',
			'sqlprj' => '`pbmid`, `title`, `statement`, `solution`',
			'sqlcnd' => "where `pbmid` in \${list} order by `pbmid` desc",
			'successmsg' => 'Problems information given successfully',
			'lsttrack' => true,
			'output' => array('entities' => 'problems'),
			'mapkey' => 'pbmid',
			'mapname' => 'problem',
			'saction' => 'add'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('problems', 'quizid', 'qzname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz');
	}
	
}

?>