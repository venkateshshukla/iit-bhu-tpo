<?php 
require_once(SBSERVICE);

/**
 *	@class StageListWorkflow
 *	@desc Returns all stages information in shortlist
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param shlstid/id long int Shortlist ID [memory] optional default 0
 *	@param shlstname string Shortlist name [memory] optional default ''
 *
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@return stages array Stages information [memory]
 *	@return shlstname string Shortlist name [memory]
 *	@return shlstid long int Shortlist ID [memory]
 *	@return admin integer Is admin [memory]
 *	@return total long int Total count [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class StageListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('shlstid' => false, 'shlstname' => false, 'id' => 0, 'name' => '', 'pgsz' => false, 'pgno' => 0, 'total' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['shlstid'] = $memory['shlstid'] ? $memory['shlstid'] : $memory['id'];
		$memory['shlstname'] = $memory['shlstname'] ? $memory['shlstname'] : $memory['name'];
		
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'shlstid'),
			'conn' => 'cbslconn',
			'relation' => '`stages`',
			'type' => 'stage',
			'sqlprj' => '`stageid`, `stage`, `name`, `start`, UNIX_TIMESTAMP(`start`)*1000 as `start_ts`, `end`, UNIX_TIMESTAMP(`end`)*1000 as `end_ts`, `open`, `status`',
			'sqlcnd' => "where `stageid` in \${list} order by `start`",
			'output' => array('entities' => 'stages'),
			'mapkey' => 'stageid',
			'mapname' => 'stage',
			'saction' => 'add',
			'successmsg' => 'Stages information given successfully'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('stages', 'shlstid', 'shlstname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz');
	}
	
}

?>