<?php 
require_once(SBSERVICE);

/**
 *	@class StageEditWorkflow
 *	@desc Edits stage for event using ID
 *
 *	@param stageid long int Stage ID [memory]
 *	@param name string Stage name [memory]
 *	@param stage integer Stage number [memory]
 *	@param open integer Is open [memory]
 *	@param start string Start time [memory] (YYYY-MM-DD hh:mm:ss format)
 *	@param end string End time [memory] (YYYY-MM-DD hh:mm:ss format)
 *	@param status integer Status [memory]
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class StageEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'stageid', 'name', 'stage', 'start', 'end', 'shlstid', 'shlstname')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$start = $memory['start'] ? "'\${start}'" : 'null';
		$end = $memory['end'] ? "'\${end}'" : 'null';
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.edit.workflow',
			'input' => array('id' => 'stageid'),
			'args' => array('name', 'stage', 'start', 'end'),
			'conn' => 'cbslconn',
			'relation' => '`stages`',
			'sqlcnd' => "set `name`='\${name}', `stage`=\${stage}, `start`=$start, `end`=$end where `stageid`=\${id}",
			'escparam' => array('name', 'start', 'end'),
			'check' => false,
			'successmag' => 'Stage edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'stageid', 'parent' => 'shlstid', 'cname' => 'name', 'pname' => 'shlstname'),
			'conn' => 'cbslconn',
			'relation' => '`stages`',
			'sqlprj' => '`stageid`, `stage`, `name`, `start`, UNIX_TIMESTAMP(`start`)*1000 as `start_ts`, `end`, UNIX_TIMESTAMP(`end`)*1000 as `end_ts`, `open`, `status`',
			'sqlcnd' => "where `stageid`=\${id}",
			'errormsg' => 'Invalid Stage ID',
			'type' => 'stage',
			'successmsg' => 'Stage information given successfully',
			'output' => array('entity' => 'stage'),
			'auth' => false,
			'track' => false,
			'sinit' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'shlstid'),
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
		return array('stageid', 'shlstid', 'shlstname', 'stage', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>