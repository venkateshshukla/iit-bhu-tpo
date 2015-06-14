<?php 
require_once(SBSERVICE);
require_once(AYROOT. 'ui/php/2012/parikshan.conf.php');

/**
 *	@class SubmissionParikshanWorkflow
 *	@desc Adds submission for parikshan 2012
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param string user Username [memory]
 *	@param questions array Question IDs [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SubmissionParikshanWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'questions')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		if($memory['keyid'] == -1){
			$memory['valid'] = false;
			$memory['msg'] = 'You must be loggedin to participate in the round';
			$memory['status'] = 403;
			$memory['details'] = 'Keyid -1 at parikshan-submit.console';
			return $memory;
		}
		
		if(Parikshan::$live === false && Parikshan::$plive === false){
			$memory['valid'] = false;
			$memory['msg'] = 'Beyond Submission Time';
			$memory['status'] = 505;
			$memory['details'] = 'Current time beyond submission time';
			return $memory;
		}
		
		$round = Parikshan::$rounds[Parikshan::$plive];
		$eid = $round['eid'];
		$questions = $memory['questions'];
		
		$service = array(
			'service' => 'transpera.relation.select.workflow',
			'args' => array('keyid'),
			'conn' => 'ayconn',
			'relation' => '`submissions`',
			'sqlprj' => '`subid`',
			'sqlcnd' => "where `eid`=$eid and `keyid`=\${keyid}",
			'type' => 'submission',
			'successmsg' => 'Duplicate submission information given successfully',
			'output' => array('result' => 'submissions'),
			'ismap' => false
		);
		
		$result = Snowblozm::run($service, $memory);
		if($result['valid']){
			$memory['valid'] = false;
			$memory['msg'] = 'Duplicate Submission';
			$memory['status'] = 503;
			$memory['details'] = 'Current submission is duplicate';
			return $memory;
		}
		
		$inserts = array();
		$options = array(0, 1, 2, 3, 4);
		unset($options[0]);
		
		foreach($questions as $q){
			if(isset($_POST['q_'.$q])){
				$ans = $_POST['q_'.$q];
				if(!isset($options[$ans]) || !is_numeric($q)){
					$memory['valid'] = false;
					$memory['msg'] = 'Invalid Submission';
					$memory['status'] = 505;
					$memory['details'] = 'Invalid Submission for Answer';
					return $memory;
				}
				$inserts[$q] = $options[$ans];
			}
		}
		
		if(count($inserts) == 0){
			$memory['valid'] = false;
			$memory['msg'] = 'No Questions Answered';
			$memory['status'] = 505;
			$memory['details'] = 'Invalid Submission for Answer';
			return $memory;
		}
		
		$query = array();
		foreach($inserts as $key => $value)
			array_push($query, "($eid, \${keyid}, $key, $value, 1)");
		$query = implode(',', $query);

		$service = array(
			'service' => 'transpera.relation.insert.workflow',
			'args' => array('keyid'),
			'conn' => 'ayconn',
			'relation' => '`submissions`',
			'sqlcnd' => "(`eid`, `keyid`, `pid`, `data`, `status`) values $query",
			'type' => 'submission'
		);
		
		$memory = Snowblozm::run($service, $memory);
		if(!$memory['valid'])
			return $memory;
		
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
