<?php 
require_once(SBSERVICE);

/**
 *	@class SubmissionScoresWorkflow
 *	@desc Returns all scores information in event container
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param eid long int Event ID [memory] optional default PORTAL_ID
 *	@param ename string Event name [memory] optional default ''
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@return scores array Profile scores information [memory]
 *	@return eid long int Event ID [memory]
 *	@return ename string Event name [memory]
 *	@return admin integer Is admin [memory]
 *	@return total long int Paging Total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SubmissionScoresWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('eid' => false, 'id' => 0, 'name' => '', 'ename' => false, 'pgsz' => 100, 'pgno' => 0, 'total' => false),
			'set' => array('id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['eid'] = $memory['eid'] ? $memory['eid'] : $memory['id'];
		$memory['ename'] = $memory['ename'] ? $memory['ename'] : $memory['name'];
		
		$workflow = array(
		array(
			'service' => 'transpera.relation.select.workflow',
			'args' => array('eid'),
			'conn' => 'ayconn',
			'relation' => '`profiles` p, `scores` s',
			'sqlprj' => 'p.`plid`, p.`name`, p.`username`, p.`org`, p.`country`, s.`score`',
			'sqlcnd' => "where s.`eid`=\${eid} and p.`ustatus`='A' and p.`owner`=s.`owner` order by s.`score` desc",
			'type' => 'score',
			'check' => false,
			'successmsg' => 'Scores information given successfully',
			'output' => array('result' => 'scores'),
			'ismap' => false
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('scores', 'eid', 'ename', 'total', 'pgsz', 'pgno');
	}
	
}

?>