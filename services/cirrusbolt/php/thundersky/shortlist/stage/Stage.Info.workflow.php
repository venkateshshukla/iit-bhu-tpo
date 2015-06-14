<?php 
require_once(SBSERVICE);

/**
 *	@class StageInfoWorkflow
 *	@desc Returns stage information by ID
 *
 *	@param stageid/id long int Stage ID [memory]
 *	@param shlstid long int Shortlist ID [memory] optional default 0
 *	@param evname string Series name [memory] optional default ''
 *	@param keyid long int Usage Key ID [memory] optional default false
 *
 *	@return stage array Stage information [memory]
 *	@return evname string Shortlist name [memory]
 *	@return shlstid long int Shortlist ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class StageInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('stageid'),
			'optional' => array('keyid' => false, 'shlstid' => 0, 'shlstname' => '', 'id' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['stageid'] = $memory['stageid'] ? $memory['stageid'] : $memory['id'];
		
		$service = array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'stageid'),
			'conn' => 'cbslconn',
			'relation' => '`stages`',
			'sqlcnd' => "where `stageid`=\${id}",
			'errormsg' => 'Invalid Stage ID', 
			'successmsg' => 'Stage information given successfully',
			'output' => array('entity' => 'stage')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('stage', 'shlstid', 'shlstname', 'admin');
	}
	
}

?>