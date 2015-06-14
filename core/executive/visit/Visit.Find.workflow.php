<?php 
require_once(SBSERVICE);

/**
 *	@class VisitFindWorkflow
 *	@desc Returns visit information by name
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
class VisitFindWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('keyid' => false, 'vstname' => false, 'user' => '', 'plname' => false, 'name' => '', 'portalid' => COMPANY_PORTAL_ID),
			'set' => array('name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['vstname'] = $memory['vstname'] ? $memory['vstname'] : $memory['name'];
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.find.workflow',
			'input' => array('parent' => 'portalid', 'cname' => 'name', 'pname' => 'plname'),
			'args' => array('vstname'),
			'conn' => 'exconn',
			'idkey' => 'visitid',
			'relation' => '`visits`',
			'sqlprj' => '`visitid`, `vstname`, `files`, `shortlist`, `vtype`, `remarks`, `issues`, `profile`, `vstatus`, `year`, `comid`, `comuser`, `bpackage`, `ipackage`, `mpackage`, `visitdate`, UNIX_TIMESTAMP(`deadline`)*1000 as `deadline_ts`, `deadline`, UNIX_TIMESTAMP(`issuedl`)*1000 as `issuedl_ts`, `issuedl`, (select c.`name` from `companies` c where c.`comid`=`comid`) as `comname`',
			'sqlcnd' => "where `vstname`='\${vstname}'",
			'errormsg' => 'Invalid Visit Name',
			'escparam' => array('vstname'),
			'type' => 'visit',
			'successmsg' => 'Visit information given successfully',
			'output' => array('entity' => 'visit', 'id' => 'visitid')
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('visit'),
			'params' => array('visit.visitid' => 'visitid')
		)/*,
		array(
			'service' => 'executive.student.list.workflow',
			'output' => array('admin' => 'stdadmin', 'padmin' => 'btadmin'),
			'padmin' => false
		)*/);
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
		
		$memory['id'] = $memory['visitid'];
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('visit', 'id', 'visitid', 'plname', 'portalid', 'admin', 'vstname', /*'students',*/ 'id', 'visitid', 'vstname', /*'stdadmin',*/ 'chain', 'total', 'pgno', 'pgsz');
	}
	
}

?>