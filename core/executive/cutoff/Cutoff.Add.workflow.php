<?php 
require_once(SBSERVICE);

/**
 *	@class CutoffAddWorkflow
 *	@desc Adds new cutoff
 *
 *	@param dept array Departments [memory]
 *	@param course array Courses [memory]
 *	@param eligibility float Eligibility [memory]
 *	@param margin float Eligibility margin [memory]
 *	@param max integer Max count [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param visitid long int Visit ID [memory] optional default 0
 *	@param vstname string Visit Name [memory] optional default ''
 *	@param level integer Web level [memory] optional default false (inherit post admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
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
class CutoffAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'dept', 'course', 'eligibility', 'margin', 'max'),
			'optional' => array('visitid' => 0, 'vstname' => '', 'level' => false, 'owner' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['verb'] = 'added';
		$memory['join'] = 'on';
		$memory['public'] = 1;
		
		$memory['dept'] = is_array($memory['dept']) ? implode(', ', $memory['dept']) : $memory['dept'];
		$memory['course'] = is_array($memory['course']) ? implode(', ', $memory['course']) : $memory['course'];
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.add.workflow',
			'args' => array('dept', 'course', 'eligibility', 'margin', 'max'),
			'input' => array('parent' => 'visitid', 'cname' => 'eligibility', 'vstname' => 'vstname'),
			'conn' => 'exconn',
			'relation' => '`cutoffs`',
			'type' => 'cutoff',
			'sqlcnd' => "(`ctfid`, `owner`, `dept`, `course`, `eligibility`, `margin`, `max`) values (\${id}, \${owner}, '\${dept}', '\${course}', \${eligibility}, \${margin}, \${max})",
			'escparam' => array('dept', 'course'),
			'successmsg' => 'Cutoff added successfully',
			'output' => array('id' => 'ctfid')
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