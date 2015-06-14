<?php 
require_once(SBSERVICE);

/**
 *	@class VisitCreateWorkflow
 *	@desc Creates willingness by visit ID
 *
 *	@param visitid/id long int Visit ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param user string Key User [memory]
 *	@param portalid long int Portal ID [memory] optional default COMPANY_PORTAL_ID
 *	@param plname/name string Portal name [memory] optional default ''
 *
 *	@return visit array Visit information [memory]
 *	@return plname string Portal name [memory]
 *	@return portalid long int Portal ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class VisitCreateWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('visitid'),
			'optional' => array('keyid' => false, 'user' => '', 'plname' => false, 'name' => '', 'portalid' => COMPANY_PORTAL_ID, 'id' => 0),
			'set' => array('id', 'name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['visitid'] = $memory['visitid'] ? $memory['visitid'] : $memory['id'];
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'portalid'),
			'action' => 'add'
		),
		array(
			'service' => 'executive.visit.info.workflow',
			'pgsz' => false,
			'padmin' => true
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
		
		if($memory['visit']['vtype'] == 'placement')
			$qry = "`year`=".$memory['visit']['year'].' and (0 ';
		else
			$qry = '((`year`='.$memory['visit']['year'].') or (`year`='.((int)$memory['visit']['year'] + 1).' and `course`=\'idd\')) and (0 ';
	
		if(count($memory['cutoffs']) == 0){
			$qry .= 'or 1';
		}
		else {
			foreach($memory['cutoffs'] as $ctof){
				$dept = $ctof['cutoff']['dept'];
				$dept = explode(', ', $dept);
				$dept = "('".implode("','", $dept)."')";
				
				$course = $ctof['cutoff']['course'];
				$course = explode(', ', $course);
				$course = "('".implode("','", $course)."')";
				
				$cgpa = $ctof['cutoff']['eligibility'] - $ctof['cutoff']['margin'];
				
				$qry .= "or (`course` in $course and `dept` in $dept and `cgpa` >= $cgpa)";
			}
		}
		
		$qry .= ")";
		
		if($memory['visit']['vtype'] == 'placement'){
			$qry .= " and (case b.`dept` when 'cer' then ".($memory['visit']['cer'] != '' ? '`'.$memory['visit']['cer']."`=''" : "1")." when 'che' then ".($memory['visit']['che'] != '' ? '`'.$memory['visit']['che']."`=''" : "1")." when 'civ' then ".($memory['visit']['civ'] != '' ? '`'.$memory['visit']['civ']."`=''" : "1")." when 'cse' then ".($memory['visit']['cse'] != '' ? '`'.$memory['visit']['cse']."`=''" : "1")." when 'eee' then ".($memory['visit']['eee'] != '' ? '`'.$memory['visit']['eee']."`=''" : "1")." when 'ece' then ".($memory['visit']['ece'] != '' ? '`'.$memory['visit']['ece']."`=''" : "1")." when 'mec' then ".($memory['visit']['mec'] != '' ? '`'.$memory['visit']['mec']."`=''" : "1")." when 'met' then ".($memory['visit']['met'] != '' ? '`'.$memory['visit']['met']."`=''" : "1")." when 'min' then ".($memory['visit']['min'] != '' ? '`'.$memory['visit']['min']."`=''" : "1")." when 'phe' then ".($memory['visit']['phe'] != '' ? '`'.$memory['visit']['phe']."`=''" : "1")." when 'apc' then ".($memory['visit']['apc'] != '' ? '`'.$memory['visit']['apc']."`=''" : "1")." when 'apm' then ".($memory['visit']['apm'] != '' ? '`'.$memory['visit']['apm']."`=''" : "1")." when 'app' then ".($memory['visit']['app'] != '' ? '`'.$memory['visit']['app']."`=''" : "1")." when 'bce' then ".($memory['visit']['bce'] != '' ? '`'.$memory['visit']['bce']."`=''" : "1")." when 'bme' then ".($memory['visit']['bme'] != '' ? '`'.$memory['visit']['bme']."`=''" : "1")." when 'mst' then ".($memory['visit']['mst'] != '' ? '`'.$memory['visit']['mst']."`=''" : "1")." else '' end)";
		}
		
		$memory = Snowblozm::run(array(
			'service' => 'transpera.relation.select.workflow',
			'conn' => 'exconn',
			'relation' => '`students` s, `grades` g, `batches` b, `slots` t',
			'sqlprj' => 's.`name`, s.`username`, b.`btname`',
			'sqlcnd' => "where s.`grade`=g.`gradeid` and s.`batchid`=b.`batchid` and s.`slot`=t.`slotid` and $qry",
			'ismap' => false,
			'check' => false,
			'output' => array('result' => 'students')
		), $memory);
		
		if(!$memory['valid'])
			return $memory;
		
		//echo json_encode($memory['students']);exit;
			
		foreach($memory['students'] as $stds){
			$memory = Snowblozm::run(array(
				'service' => 'executive.willingness.add.workflow',
				'username' => $stds['username'],
				'name' => $memory['visit']['vstname'],
				'btname' => $stds['btname'],
			), $memory);
		}
		
		$memory['valid'] = true;
		$memory['status'] = 200;
		$memory['msg'] = 'Auto Creation Successful';
		$memory['details'] = 'Successfully executed @visit.create';
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