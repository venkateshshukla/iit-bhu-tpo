<?php 
require_once(SBSERVICE);
require_once(AYROOT. 'ui/php/2012/prayaas-mathematical.conf.php');

/**
 *	@class ScoreboardPrayaasWorkflow
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
class ScoreboardPrayaasWorkflow implements Service {
	
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
		
		$round = Prayaas_M::$rounds[$memory['key']];
		$memory['eid'] = $eid = $round['eid'];
		$memory['ename'] = 'Prayaas '.$round['name'];
		$start = $round['start'];
		$end = $round['end'];
		
		$puzzles = array_merge($round['puzzles'], $round['bonuses']);
		$memory['pbmcount'] = $pbmcnt = count($puzzles);
		$indices = array();
		foreach($puzzles as $i => $p){
			$indices[$p] = $i;
		}
		//echo json_encode($indices);exit;
		$workflow = array(
		array(
			'service' => 'transpera.relation.select.workflow',
			'conn' => 'ayconn',
			'relation' => '`profiles` p, `submissions` s, `puzzles` z',
			'sqlprj' => "p.`plid`, p.`name`, p.`username`, p.`org`, p.`country`, s.`pid`, s.`data`, s.`status`, (UNIX_TIMESTAMP(s.`ts`) - $start)*1000 as `ts`, 
				case 
					when s.`status`<2 and s.`data`=cast(z.`shortans` as unsigned) then z.`spoints`
					when s.`status`=2 and UNIX_TIMESTAMP(now()) < $end then z.`lpoints`
					when s.`status`=2 and UNIX_TIMESTAMP(now()) > $end and s.`data`=cast(z.`longans` as unsigned) then z.`lpoints`
					else 0
				end as `points`
			",
			'sqlcnd' => "where s.`eid`=$eid and p.`ustatus`='A' and p.`owner`=s.`keyid` and s.`pid`=z.`pzlid` and s.`subid`=(select max(`subid`) from `submissions` where `pid`=s.`pid` and `keyid`=s.`keyid` and ((s.`status`=2 and `status`=2) or (s.`status`<2 and `status`<2))) group by s.`keyid`, s.`pid`, s.`status`",
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

			if(!isset($result[$plid]['answers'])){
				$result[$plid]['answers'] = array();
				for($i=0; $i<$pbmcnt; $i++){
					$result[$plid]['answers'][$i] = array();
				}
			}
			
			if($score['status'] < 2){
				$result[$plid]['answers'][$indices[$pid]]['small'] = array(
					'points' => $score['points'],
					'ts' => $score['ts']
				);
				
				$result[$plid]['score'] += $score['points'];
				if($score['points'] && ($score['ts'] > $result[$plid]['tslast'])){
					$result[$plid]['tslast'] = $score['ts'];
				}
			}
			elseif($score['status'] == 2){
				$result[$plid]['answers'][$indices[$pid]]['large'] = array(
					'points' => $score['points'],
					'ts' => $score['ts']
				);
				
				$result[$plid]['score'] += $score['points'];
				if($score['points'] && ($score['ts'] > $result[$plid]['tslast'])){
					$result[$plid]['tslast'] = $score['ts'];
				}
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
		return array('scores', 'pbmcount', 'eid', 'ename', 'total', 'pgsz', 'pgno');
	}
	
}

?>