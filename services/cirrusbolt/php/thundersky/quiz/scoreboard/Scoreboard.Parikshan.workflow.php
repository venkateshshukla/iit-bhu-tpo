<?php 
require_once(SBSERVICE);
require_once(AYROOT. 'ui/php/2012/parikshan.conf.php');

/**
 *	@class ScoreboardParikshanWorkflow
 *	@desc Returns all scores information in prayaas mathematical event
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param ename string Event key name [memory] optional default ''
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
class ScoreboardParikshanWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('name' => '', 'ename' => false, 'key' => '', 'pgsz' => false, 'pgno' => 0, 'total' => false),
			'set' => array('id', 'name', 'year', 'key')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['ename'] = $memory['ename'] ? $memory['ename'] : $memory['name'];
		
		$round = Parikshan::$rounds[$memory['key']];
		$memory['eid'] = $eid = $round['eid'];
		$memory['ename'] = 'Parikshan '.$round['name'];
		$start = $round['start'];
		
		$workflow = array(
		array(
			'service' => 'transpera.relation.select.workflow',
			'conn' => 'ayconn',
			'relation' => '`profiles` p, `submissions` s, `questions` q',
			'sqlprj' => "p.`plid`, p.`name`, p.`username`, p.`org`, p.`country`, s.`pid`, s.`data`, s.`status`, (UNIX_TIMESTAMP(s.`ts`) - $start)*1000 as `ts`, 
				case 
					when cast(s.`data` as unsigned)=q.`answer` then 2
					else -1
				end as `points`
			",
			'sqlcnd' => "where s.`eid`=$eid and p.`ustatus`='A' and p.`owner`=s.`keyid` and s.`pid`=q.`qstid`",
			'type' => 'score',
			'check' => false,
			'successmsg' => 'Scores information given successfully',
			'output' => array('result' => 'scores'),
			'ismap' => false
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
		
		$scores = $memory['scores'];
		$result = array();
		foreach($scores as $score){
			$plid = $score['plid'];
			$pid = $score['pid'];
			
			if(!isset($result[$plid])){
				$result[$plid] = $score;
				$result[$plid]['score'] = 0;
				$result[$plid]['tslast'] = 0;
			}
			
			$result[$plid]['score'] += $score['points'];
			if($score['points'] > 0 && ($score['ts'] > $result[$plid]['tslast'])){
				$result[$plid]['tslast'] = $score['ts'];
			}
		}
		
		function cmp($a, $b){
			return $b['score'] - $a['score'] ? $b['score'] - $a['score'] : $a['tslast'] - $b['tslast'];
		}
		
		uasort($result, 'cmp');
		$memory['scores'] = array_values($result);
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('scores', 'eid', 'ename', 'total', 'pgsz', 'pgno');
	}
	
}

?>