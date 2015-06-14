<?php 
require_once(SBSERVICE);

/**
 *	@class VisitEditWorkflow
 *	@desc Edits visit using ID
 *
 *	@param visitid long int Visit ID [memory]
 *	@param vstname string Visit name [memory]
 *	@param year string Academic Session Year [memory] 
 *	@param vtype string Type [memory] ('placement', 'internship', 'ppo')
 *	@param bpackage float B. Tech. Package [memory] optional default 0
 *	@param ipackage float IDD/IMD Package [memory] optional default 0
 *	@param mpackage float M. Tech. Package [memory] optional default 0
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
class VisitEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'visitid', 'vstname', 'year', 'vtype', 'visitdate', 'deadline', 'issuedl', 'portalid', 'plname'),
			'optional' => array('remarks' => '', 'issues' => '', 'profile' => '', 'vstatus' => 'Open for Willingness', 'bpackage' => 0, 'ipackage' => 0, 'mpackage' => 0, 'cer' => '', 'che' => '', 'civ' => '', 'cse' => '', 'eee' => '', 'ece' => '', 'mec' => '', 'met' => '', 'min' => '', 'phe' => '', 'apc' => '', 'apm' => '', 'app' => '', 'bce' => '', 'bme' => '', 'mst' => '')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){		
		$memory['public'] = 1;
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('vstname', 'year', 'vtype', 'remarks', 'issues', 'profile', 'vstatus', 'bpackage', 'ipackage', 'mpackage', 'visitdate', 'deadline', 'issuedl', 'cer', 'che', 'civ', 'cse', 'eee', 'ece', 'mec', 'met', 'min', 'phe', 'apc', 'apm', 'app', 'bce', 'bme', 'mst'),
			'input' => array('id' => 'visitid', 'cname' => 'vstname', 'parent' => 'portalid', 'pname' => 'plname'),
			'conn' => 'exconn',
			'relation' => '`visits`',
			'type' => 'visit',
			'sqlcnd' => "set `vstname`='\${vstname}', `year`='\${year}', `vtype`='\${vtype}', `remarks`='\${remarks}', `issues`='\${issues}', `profile`='\${profile}', `vstatus`='\${vstatus}', `bpackage`=\${bpackage}, `ipackage`=\${ipackage}, `mpackage`=\${mpackage}, `visitdate`='\${visitdate}', `deadline`='\${deadline}', `issuedl`='\${issuedl}', `cer`='\${cer}', `che`='\${che}', `civ`='\${civ}', `cse`='\${cse}', `eee`='\${eee}', `ece`='\${ece}', `mec`='\${mec}', `met`='\${met}', `min`='\${min}', `phe`='\${phe}', `apc`='\${apc}', `apm`='\${apm}', `app`='\${app}', `bce`='\${bce}', `bme`='\${bme}', `mst`='\${mst}' where `visitid`=\${id}",
			'escparam' => array('remarks', 'issues', 'profile', 'vstatus', 'vstname', 'year', 'vtype', 'visitdate', 'deadline', 'issuedl', 'cer', 'che', 'civ', 'cse', 'eee', 'ece', 'mec', 'met', 'min', 'phe', 'apc', 'apm', 'app', 'bce', 'bme', 'mst'),
			'check' => false,
			'successmsg' => 'Visit edited successfully'
		),
		array(
			'service' => 'executive.visit.freeze.workflow',
			'unfreeze' => $memory['vstatus'] == 'Open for Willingness'
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