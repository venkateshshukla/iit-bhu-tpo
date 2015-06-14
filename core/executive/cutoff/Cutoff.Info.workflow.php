<?php 
require_once(SBSERVICE);

/**
 *	@class CutoffInfoWorkflow
 *	@desc Returns cutoff information by ID
 *
 *	@param ctfid/id long int Cutoff ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param user string Key User [memory]
 *	@param visitid long int Visit ID [memory] optional default 0
 *	@param vstname/name string Visit name [memory] optional default ''
 *
 *	@return cutoff array Cutoff information [memory]
 *	@return vstname string Visit name [memory]
 *	@return visitid long int Visit ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class CutoffInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('ctfid'),
			'optional' => array('keyid' => false, 'user' => '', 'vstname' => false, 'name' => '', 'visitid' => false, 'id' => 0),
			'set' => array('id', 'name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['ctfid'] = $memory['ctfid'] ? $memory['ctfid'] : $memory['id'];
		
		$service = array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'ctfid', 'parent' => 'visitid', 'cname' => 'name', 'vstname' => 'vstname'),
			'conn' => 'exconn',
			'relation' => '`cutoffs`',
			'sqlprj' => '`ctfid`, `dept`, `course`, `eligibility`, `margin`, `max`',
			'sqlcnd' => "where `ctfid`=\${id}",
			'errormsg' => 'Invalid Cutoff ID',
			'type' => 'cutoff',
			'successmsg' => 'Cutoff information given successfully',
			'output' => array('entity' => 'cutoff')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('cutoff', 'vstname', 'visitid', 'admin');
	}
	
}

?>