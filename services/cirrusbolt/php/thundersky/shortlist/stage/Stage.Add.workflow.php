<?php 
require_once(SBSERVICE);

/**
 *	@class StageAddWorkflow
 *	@desc Adds new stage for shortlist
 *
 *	@param name string Stage name [memory]
 *	@param stage integer Stage number [memory]
 *	@param open integer Is open [memory] optional default 0
 *	@param start string Start time [memory] (YYYY-MM-DD hh:mm:ss format)
 *	@param end string End time [memory] (YYYY-MM-DD hh:mm:ss format)
 *	@param status integer Status [memory] optional default 1
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param shlstid long int Shortlist ID [memory] optional default 0
 *	@param level integer Web level [memory] optional default 1 (shortlist admin access allowed)
 *
 *	@return stageid long int Stage ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class StageAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'name', 'stage', 'start', 'end'),
			'optional' => array('open' => 0, 'status' => 1, 'shlstid' => 0, 'shlstname' => '', 'level' => 1)
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
			'service' => 'transpera.entity.add.workflow',
			'input' => array('parent' => 'shlstid'),
			'output' => array('id' => 'stageid'),
			'args' => array('name', 'stage', 'start', 'end', 'open', 'status'),
			'conn' => 'cbslconn',
			'relation' => '`stages`',
			'type' => 'stage',
			'sqlcnd' => "(`stageid`, `owner`, `name`, `stage`, `start`, `end`, `open`, `status`) values (\${id}, \${owner}, '\${name}', \${stage}, $start, $end, \${open}, \${status})",
			'escparam' => array('name', 'start', 'end'),
			'successmsg' => 'Stage added successfully'
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