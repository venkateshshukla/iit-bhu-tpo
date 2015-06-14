<?php 
require_once(SBSERVICE);

/**
 *	@class VisitInfoWorkflow
 *	@desc Returns visit information by ID
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
class VisitInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('visitid'),
			'optional' => array('keyid' => false, 'user' => '', 'plname' => false, 'name' => '', 'portalid' => COMPANY_PORTAL_ID, 'id' => 0, 'pgsz' => 5, 'pgno' => 0, 'total' => false, 'padmin' => false),
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
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'visitid', 'parent' => 'portalid', 'cname' => 'name', 'pname' => 'plname'),
			'conn' => 'exconn',
			'relation' => '`visits`',
			'sqlprj' => '`visitid`, `vstname`, `files`, `shortlist`, `vtype`, `year`, `comid`, `comuser`, `remarks`, `issues`, `profile`, `vstatus`, `bpackage`, `ipackage`, `mpackage`, `visitdate`, UNIX_TIMESTAMP(`deadline`)*1000 as `deadline_ts`, `deadline`, UNIX_TIMESTAMP(`issuedl`)*1000 as `issuedl_ts`, `issuedl`, (select c.`name` from `companies` c where c.`comid`=`visits`.`comid`) as `comname`, `cer`, `che`, `civ`, `cse`, `eee`, `ece`, `mec`, `met`, `min`, `phe`, `apc`, `apm`, `app`, `bce`, `bme`, `mst`',
			'sqlcnd' => "where `visitid`=\${id}",
			'errormsg' => 'Invalid Visit ID',
			'type' => 'visit',
			'successmsg' => 'Visit information given successfully',
			'output' => array('entity' => 'visit'),
			'auth' => $memory['padmin'] !== true,
			'track' => false,
			'sinit' => $memory['padmin'] !== true
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('visit'),
			'params' => array('visit.vstname' => 'vstname', 'visit.comid' => 'comid', 'visit.files' => 'files', 'visit.shortlist' => 'shortlist')
		),
		array(
			'service' => 'executive.cutoff.list.workflow',
			'output' => array('admin' => 'ctfadmin', 'padmin' => 'vstadmin'),
			'padmin' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'visitid'),
			'output' => array('chain' => 'pchain')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('visit', 'cutoffs', 'plname', 'portalid', 'ctfadmin', 'vstname', 'files', 'shortlist', 'comid', 'chain', 'pchain', 'admin', 'padmin', 'total', 'pgsz', 'pgno');
	}
	
}

?>