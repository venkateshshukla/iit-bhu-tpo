<?php 
require_once(SBSERVICE);

/**
 *	@class VisitAddWorkflow
 *	@desc Adds new visit
 *
 *	@param year string Academic Session Year [memory] 
 *	@param type string Type [memory] ('placement', 'internship', 'ppo')
 *	@param bpackage float B. Tech. Package [memory] optional default 0
 *	@param ipackage float IDD/IMD Package [memory] optional default 0
 *	@param mpackage float M. Tech. Package [memory] optional default 0
 *	@param comuser string Company username [memory]
 *	@param visitdate string Date of visit [memory] (Format YYYY-MM-DD)
 *	@param deadline string Deadline [memory] (Format YYYY-MM-DD)
 *	@param cer string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param che string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param civ string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param cse string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param eee string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param ece string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param mec string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param met string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param min string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param phe string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param apc string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param apm string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param app string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param bce string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param bme string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *	@param mst string Requirement [memory] ('mr', 'ordinary', 'dream', 'super')
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param portalid long int Portal ID [memory] optional default COMPANY_PORTAL_ID
 *	@param plname string Portal Name [memory] optional default ''
 *	@param level integer Web level [memory] optional default false (inherit portal admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
 *
 *	@return visitid long int Visit ID [memory]
 *	@return portalid long int Portal ID [memory]
 *	@return plname string Portal Name [memory]
 *	@return visit array Visit information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class VisitAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'year', 'vtype', 'comuser', 'deadline', 'issuedl'),
			'optional' => array('portalid' => COMPANY_PORTAL_ID, 'visitdate' => '0000-00-00', 'plname' => '', 'level' => false, 'owner' => false, 'remarks' => '', 'issues' => '', 'profile' => '', 'vstatus' => 'Open for Willingness', 'bpackage' => 0, 'ipackage' => 0, 'mpackage' => 0, 'cer' => '', 'che' => '', 'civ' => '', 'cse' => '', 'eee' => '', 'ece' => '', 'mec' => '', 'met' => '', 'min' => '', 'phe' => '', 'apc' => '', 'apm' => '', 'app' => '', 'bce' => '', 'bme' => '', 'mst' => '')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['verb'] = 'added';
		$memory['join'] = 'on';
		$memory['public'] = 1;
		
		$memory['year'] = $memory['vtype'] == 'internship' ? $memory['year'] + 1 : $memory['year'];
		
		$memory['vstname'] = $memory['comuser'].'.'.$memory['profile'].'.'.$memory['vtype'].'.'.$memory['year'];
		$memory['bpackage'] = $memory['bpackage'] ? $memory['bpackage'] : 0;
		$memory['ipackage'] = $memory['ipackage'] ? $memory['ipackage'] : 0;
		$memory['mpackage'] = $memory['mpackage'] ? $memory['mpackage'] : 0;
		
		$workflow = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('comuser'),
			'conn' => 'exconn',
			'relation' => '`companies`',
			'sqlcnd' => "where `username`='\${comuser}'",
			'escparam' => array('comuser'),
			'errormsg' => 'Invalid Company Username',
			'successmsg' => 'Company information given successfully'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0' => 'company', 'result.0.comid' => 'comid', 'result.0.owner' => 'comowner', 'company.folder' => 'folder', 'company.name' => 'comname')
		),
		array(
			'service' => 'transpera.entity.add.workflow',
			'args' => array('vstname', 'year', 'vtype', 'remarks', 'issues', 'profile', 'vstatus', 'bpackage', 'ipackage', 'mpackage', 'comid', 'comuser', 'visitdate', 'deadline', 'issuedl', 'comowner', 'cer', 'che', 'civ', 'cse', 'eee', 'ece', 'mec', 'met', 'min', 'phe', 'apc', 'apm', 'app', 'bce', 'bme', 'mst'),
			'input' => array('parent' => 'portalid', 'cname' => 'vstname', 'pname' => 'plname'),
			'conn' => 'exconn',
			'relation' => '`visits`',
			'type' => 'visit',
			'authorize' => 'edit:add:remove:',
			'sqlcnd' => "(`visitid`, `owner`, `comid`, `comuser`, `vstname`, `year`, `remarks`, `issues`, `profile`, `vstatus`, `vtype`, `bpackage`, `ipackage`, `mpackage`, `visitdate`, `deadline`, `issuedl`, `files`, `shortlist`, `cer`, `che`, `civ`, `cse`, `eee`, `ece`, `mec`, `met`, `min`, `phe`, `apc`, `apm`, `app`, `bce`, `bme`, `mst`) values (\${id}, \${owner}, \${comid}, '\${comuser}', '\${vstname}', '\${year}', '\${remarks}', '\${issues}', '\${profile}', '\${vstatus}', '\${vtype}', \${bpackage}, \${ipackage}, \${mpackage}, '\${visitdate}', '\${deadline}', '\${issuedl}', \${files}, \${shortlist}, '\${cer}', '\${che}', '\${civ}', '\${cse}', '\${eee}', '\${ece}', '\${mec}', '\${met}', '\${min}', '\${phe}', '\${apc}', '\${apm}', '\${app}', '\${bce}', '\${bme}', '\${mst}')",
			'escparam' => array('vstname', 'year', 'remarks', 'issues', 'profile', 'vstatus', 'vtype', 'visitdate', 'deadline', 'issuedl', 'comuser', 'cer', 'che', 'civ', 'cse', 'eee', 'ece', 'mec', 'met', 'min', 'phe', 'apc', 'apm', 'app', 'bce', 'bme', 'mst'),
			'successmsg' => 'Visit added successfully',
			'construct' => array(
				array(
					'service' => 'storage.directory.add.workflow',
					'name' => $memory['year'].'-'.$memory['vtype'].'-'.$memory['visitdate'],
					'path' => 'storage/private/folders/'.$memory['comuser'].'/'.$memory['year'].'-'.$memory['vtype'].'-'.$memory['visitdate'].'/',
					'input' => array('stgid' => 'comid', 'owner' => 'comowner'),
					'authorize' => 'add:remove:edit:grlist',
					'grlevel' => -2,
					'grroot' => STUDENT_PORTAL_ID,
					'output' => array('dirid' => 'files')
				),
				array(
					'service' => 'transpera.reference.add.workflow',
					'type' => 'shortlist',
					'authorize' => 'add:remove:edit:grlist',
					'grlevel' => -2,
					'grroot' => STUDENT_PORTAL_ID,
					'input' => array('owner' => 'comowner'),
					'output' => array('id' => 'shortlist')
				)
			),
			'cparam' => array('files', 'shortlist'),
			'output' => array('id' => 'visitid')
		),
		array(
			'service' => 'executive.visit.info.workflow',
			'pgsz' => 5,
			'padmin' => true
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
		return array('visitid', 'visit', 'cutoffs', 'plname', 'portalid', 'ctfadmin', 'vstname', 'files', 'shortlist', 'comid', 'chain', 'pchain', 'admin', 'padmin', 'total', 'pgsz', 'pgno');
	}
	
}

?>