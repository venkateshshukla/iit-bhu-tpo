<?php 
require_once(SBSERVICE);

/**
 *	@class StageSelectionWorkflow
 *	@desc Returns all selections information in stage
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param shlstid long int Shortlist ID [memory] optional default 0
 *	@param shlstname string Shortlist Name [memory] optional default ''
 *	@param stageid long int Stage ID [memory]
 *
 *	@return selections array Selections information [memory]
 *	@return shlstid long int Shortlist ID [memory] optional default 0
 *	@return shlstname string Shortlist Name [memory] optional default ''
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class StageSelectionWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'stageid'),
			'optional' => array('shlstid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'shlstid'),
			'args' => array('stageid'),
			'conn' => 'cbslconn',
			'relation' => '`selections`',
			'sqlcnd' => "where `shlstid` in \${list} and stageid=\${stageid}",
			'type' => 'selection',
			'successmsg' => 'Selections information given successfully',
			'output' => array('entities' => 'selections'),
			'mapkey' => 'selid',
			'mapname' => 'selection'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('selections', 'shlstid', 'shlstname', 'admin');
	}
	
}

?>