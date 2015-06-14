<?php 
require_once(SBSERVICE);

/**
 *	@class CutoffEditWorkflow
 *	@desc Edits cutoff using ID
 *
 *	@param ctfid long int Cutoff ID [memory]
 *	@param dept array Departments [memory]
 *	@param course array Courses [memory]
 *	@param eligibility float Eligibility [memory]
 *	@param margin float Eligibility margin [memory]
 *	@param max integer Max count [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param visitid long int Visit ID [memory] optional default 0
 *	@param vstname string Visit Name [memory] optional default ''
 *
 *	@return ctfid long int Cutoff ID [memory]
 *	@return visitid long int Visit ID [memory]
 *	@return vstname string Visit Name [memory]
 *	@return cutoff array Cutoff information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class CutoffEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'ctfid', 'dept', 'course', 'eligibility', 'margin', 'max', 'visitid', 'vstname')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){		
		$memory['public'] = 1;
		
		$memory['dept'] = implode(', ', $memory['dept']);
		$memory['course'] = implode(', ', $memory['course']);
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('dept', 'course', 'eligibility', 'margin', 'max'),
			'input' => array('id' => 'ctfid', 'cname' => 'eligibility', 'parent' => 'visitid', 'vstname' => 'vstname'),
			'conn' => 'exconn',
			'relation' => '`cutoffs`',
			'type' => 'cutoff',
			'sqlcnd' => "set `dept`='\${dept}', `course`='\${course}', `eligibility`=\${eligibility}, `margin`=\${margin}, `max`=\${max} where `ctfid`=\${id}",
			'escparam' => array('dept', 'course'),
			'check' => false,
			'successmsg' => 'Cutoff edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'ctfid', 'parent' => 'visitid', 'cname' => 'name', 'vstname' => 'vstname'),
			'conn' => 'exconn',
			'relation' => '`cutoffs`',
			'sqlprj' => '`ctfid`, `dept`, `course`, `eligibility`, `margin`, `max`',
			'sqlcnd' => "where `ctfid`=\${id}",
			'errormsg' => 'Invalid Cutoff ID',
			'type' => 'cutoff',
			'successmsg' => 'Cutoff information given successfully',
			'output' => array('entity' => 'cutoff'),
			'auth' => false,
			'track' => false,
			'sinit' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'visitid'),
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
		return array('ctfid', 'visitid', 'vstname', 'cutoff', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>