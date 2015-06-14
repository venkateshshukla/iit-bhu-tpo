<?php 
require_once(SBSERVICE);
require_once(AYROOT. 'ui/php/2012/prayaas-mathematical.conf.php');

/**
 *	@class SubmissionPrayaasWorkflow
 *	@desc Adds submission for prayaas mathematical 2012
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param string user Username [memory]
 *	@param pzlid long int Puzzle ID [memory]
 *	@param anstype string Answer type [memory]
 *	@param answer long int Answer [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SubmissionPrayaasWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'pzlid', 'anstype', 'answer')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		if($memory['keyid'] == -1){
			$memory['valid'] = false;
			$memory['msg'] = 'You must be loggedin to participate in the event';
			$memory['status'] = 403;
			$memory['details'] = 'Keyid -1 at parikshan-submit.console';
			return $memory;
		}
		
		if(Prayaas_M::$live === false && Prayaas_M::$plive === false){
			$memory['valid'] = false;
			$memory['msg'] = 'Beyond Submission Time';
			$memory['status'] = 505;
			$memory['details'] = 'Current time beyond submission time';
			return $memory;
		}
		
		$round = Prayaas_M::$rounds[Prayaas_M::$plive];
		$pzlid = $memory['pzlid'];
		if(!in_array($pzlid, $round['puzzles']) && (Prayaas_M::$blive === false || $pzlid != Prayaas_M::$blive['puzzle'])){
			$memory['valid'] = false;
			$memory['msg'] = 'Beyond Bonus Submission Time';
			$memory['status'] = 505;
			$memory['details'] = 'Current time beyond bonus submission time';
			return $memory;
		}
		
		$eid = $round['eid'];
		
		switch($memory['anstype']){
			case 'short' :
				$service = array(
					'service' => 'transpera.relation.select.workflow',
					'args' => array('keyid', 'pzlid'),
					'conn' => 'ayconn',
					'relation' => '`submissions`',
					'sqlprj' => '`subid`, `status`',
					'sqlcnd' => "where `eid`=$eid and `keyid`=\${keyid} and `pid`=\${pzlid} and (`status`=0 or `status`=1)",
					'type' => 'submission',
					'successmsg' => 'Duplicate submission information given successfully',
					'output' => array('result' => 'submissions'),
					'check' => false,
					'ismap' => false
				);
				
				$result = Snowblozm::run($service, $memory);
				if(!$result['valid']){
					$memory['valid'] = false;
					$memory['msg'] = 'Error in selecting submissions';
					$memory['status'] = 503;
					$memory['details'] = 'Error in database';
					return $memory;
				}
				
				$submissions = $result['submissions'];
				$total = count($submissions);
				
				if($total >= 20){
					$memory['valid'] = false;
					$memory['msg'] = 'You have reached the maximum limit of 20 submissions.';
					$memory['status'] = 503;
					$memory['details'] = 'Current submission is more than 20';
					return $memory;
				}
				
				foreach($submissions as $sub){
					if($sub['status'] == 1){
						$memory['valid'] = true;
						$memory['msg'] = 'You have already submitted a correct answer.';
						$memory['status'] = 200;
						$memory['details'] = 'Successfully Executed';
						return $memory;
					}
				}
				
				$service = array(
					'service' => 'transpera.relation.insert.workflow',
					'args' => array('keyid', 'pzlid', 'answer'),
					'conn' => 'ayconn',
					'relation' => '`submissions`',
					'sqlcnd' => "(`eid`, `keyid`, `pid`, `data`, `status`) values ($eid, \${keyid}, \${pzlid}, \${answer}, 0)",
					'type' => 'submission',
					'output' => array('id' => 'subid')
				);
				
				$memory = Snowblozm::run($service, $memory);
				if(!$memory['valid'])
					return $memory;
				
				$service = array(
					'service' => 'transpera.relation.unique.workflow',
					'args' => array('pzlid', 'answer'),
					'conn' => 'ayconn',
					'relation' => '`puzzles`',
					'sqlprj' => '`pzlid`',
					'sqlcnd' => "where `pzlid`=\${pzlid} and `shortans`=\${answer}",
					'type' => 'puzzle',
					'successmsg' => 'Puzzles information given successfully',
					'errormsg' => 'Your answer is wrong. Please try again.',
					'output' => array('result' => 'puzzles'),
					'ismap' => false
				);
				
				$memory = Snowblozm::run($service, $memory);
				if(!$memory['valid'])
					return $memory;
				
				$service = array(
					'service' => 'transpera.relation.update.workflow',
					'args' => array('subid'),
					'conn' => 'ayconn',
					'relation' => '`submissions`',
					'sqlcnd' => "set `status`=1 where `subid`=\${subid}",
					'type' => 'submission'
				);
				
				$memory = Snowblozm::run($service, $memory);
				if(!$memory['valid'])
					return $memory;
				
				$memory['valid'] = true;
				$memory['msg'] = 'Congratulations, your answer is correct. <br /> You may now attempt the large case.';
				$memory['status'] = 200;
				$memory['details'] = 'Successfully Executed';
				return $memory;
				break;
				
			case 'long' :
				$service = array(
					'service' => 'transpera.relation.select.workflow',
					'args' => array('keyid', 'pzlid'),
					'conn' => 'ayconn',
					'relation' => '`submissions`',
					'sqlprj' => '`subid`, `status`',
					'sqlcnd' => "where `eid`=$eid and `keyid`=\${keyid} and `pid`=\${pzlid} and (`status`=2)",
					'type' => 'submission',
					'successmsg' => 'Duplicate submission information given successfully',
					'output' => array('result' => 'submissions'),
					'check' => false,
					'ismap' => false
				);
				
				$result = Snowblozm::run($service, $memory);
				if(!$result['valid']){
					$memory['valid'] = false;
					$memory['msg'] = 'Error in selecting submissions';
					$memory['status'] = 503;
					$memory['details'] = 'Error in database';
					return $memory;
				}
				
				$submissions = $result['submissions'];
				$total = count($submissions);
				
				if($total >= 20){
					$memory['valid'] = false;
					$memory['msg'] = 'You have reached the maximum limit of 20 submissions.';
					$memory['status'] = 503;
					$memory['details'] = 'Current submission is more than 20';
					return $memory;
				}
				
				/*foreach($submissions as $sub){
					if($sub['status'] == 1){
						$memory['valid'] = true;
						$memory['msg'] = 'You have already submitted a correct answer.';
						$memory['status'] = 200;
						$memory['details'] = 'Successfully Executed';
						return $memory;
					}
				}*/
				
				$service = array(
					'service' => 'transpera.relation.unique.workflow',
					'args' => array('keyid', 'pzlid'),
					'conn' => 'ayconn',
					'relation' => '`submissions`',
					'sqlprj' => '`subid`, `status`',
					'sqlcnd' => "where `eid`=$eid and `keyid`=\${keyid} and `pid`=\${pzlid} and (`status`=1)",
					'type' => 'submission',
					'successmsg' => 'Duplicate submission information given successfully',
					'output' => array('result' => 'submissions'),
					'ismap' => false
				);
				
				$result = Snowblozm::run($service, $memory);
				if(!$result['valid']){
					$memory['valid'] = false;
					$memory['msg'] = 'You have not answered short case correctly.';
					$memory['status'] = 503;
					$memory['details'] = 'Error in database';
					return $memory;
				}
				
				$service = array(
					'service' => 'transpera.relation.insert.workflow',
					'args' => array('keyid', 'pzlid', 'answer'),
					'conn' => 'ayconn',
					'relation' => '`submissions`',
					'sqlcnd' => "(`eid`, `keyid`, `pid`, `data`, `status`) values ($eid, \${keyid}, \${pzlid}, \${answer}, 2)",
					'type' => 'submission',
					'output' => array('id' => 'subid')
				);
				
				$memory = Snowblozm::run($service, $memory);
				if(!$memory['valid'])
					return $memory;
				
				/*$service = array(
					'service' => 'transpera.relation.unique.workflow',
					'args' => array('pzlid', 'answer'),
					'conn' => 'ayconn',
					'relation' => '`puzzles`',
					'sqlprj' => '`pzlid`',
					'sqlcnd' => "where `pzlid`=\${pzlid} and `shortans`=\${answer}",
					'type' => 'puzzle',
					'successmsg' => 'Puzzles information given successfully',
					'errormsg' => 'Your answer is wrong. Please try again.',
					'output' => array('result' => 'puzzles'),
					'ismap' => false
				);
				
				$memory = Snowblozm::run($service, $memory);
				if(!$memory['valid'])
					return $memory;
				
				$service = array(
					'service' => 'transpera.relation.update.workflow',
					'args' => array('subid'),
					'conn' => 'ayconn',
					'relation' => '`submissions`',
					'sqlcnd' => "set `status`=1 where `subid`=\${subid}",
					'type' => 'submission'
				);
				
				$memory = Snowblozm::run($service, $memory);
				if(!$memory['valid'])
					return $memory;*/
				
				$memory['valid'] = true;
				$memory['msg'] = 'Your answer has been accepted successfully.';
				$memory['status'] = 200;
				$memory['details'] = 'Successfully Executed';
				return $memory;
				
				break;
			
			default :
				$memory['valid'] = false;
				$memory['msg'] = 'Invalid Request.';
				$memory['status'] = 503;
				$memory['details'] = 'Invalid value of anstype :'.$memory['anstype'];
				return $memory;
		}
		
		$memory['valid'] = true;
		$memory['msg'] = 'Your submission was successfully accepted. Thanks for participating.';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully Executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>
