<?php 
require_once(SBSERVICE);

/**
 *	@class VisitCollectWorkflow
 *	@desc Collects willingness by visit ID
 *
 *	@param visitid/id long int Visit ID [memory]
 *	@param shlstid/id Shortlist ID [memory]
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
class VisitCollectWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('visitid', 'shlstid'),
			'optional' => array('keyid' => false, 'user' => '', 'plname' => false, 'name' => '', 'portalid' => COMPANY_PORTAL_ID, 'id' => 0),
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
			'service' => 'executive.willingness.list.workflow',
			'input' => array('wgltid' => 'visitid'),
			'wgltname' => ''
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
		
		foreach($memory['willingnesses'] as $wlgs){
			$memory = Snowblozm::run(array(
				'service' => 'executive.selection.add.workflow',
				'username' => $wlgs['willingness']['username'],
				'name' => $wlgs['willingness']['wname'],
				'btname' => $wlgs['willingness']['batch'],
			), $memory);
		}
		
		$memory = Snowblozm::run(array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('visitid'),
			'conn' => 'exconn',
			'relation' => '`selections`',
			'sqlcnd' => "set `resume`=(select w.`resume` from `willingnesses` w where w.`owner`=`owner` and w.`visitid`=`visitid`) where `visitid`=\${visitid}",
			'check' => false
		), $memory);
		
		$memory['valid'] = true;
		$memory['status'] = 200;
		$memory['msg'] = 'Auto Creation Successful';
		$memory['details'] = 'Successfully executed @visit.collect';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>