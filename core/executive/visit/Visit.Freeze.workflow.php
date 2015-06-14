<?php 
require_once(SBSERVICE);

/**
 *	@class VisitFreezeWorkflow
 *	@desc Freezes willingness by visit ID
 *
 *	@param visitid/id long int Visit ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param unfreese boolean Is unfreeze [memory] optional default false
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
class VisitFreezeWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('visitid'),
			'optional' => array('keyid' => false, 'user' => '', 'plname' => false, 'name' => '', 'portalid' => COMPANY_PORTAL_ID, 'id' => 0, 'unfreeze' => false),
			'set' => array('id', 'name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['visitid'] = $memory['visitid'] ? $memory['visitid'] : $memory['id'];
		$memory['msg'] = ($memory['unfreeze'] ? 'Unfreeze' : 'Freeze').' Successful';
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'portalid'),
			'action' => 'add'
		),
		array(
			'service' => 'executive.visit.info.workflow',
			'pgsz' => false,
			'padmin' => true
		),
		array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('visitid'),
			'input' => array('id' => 'visitid', 'cname' => 'vstname'),
			'conn' => 'exconn',
			'relation' => '`willingnesses`',
			'type' => 'willingness',
			'sqlcnd' => "set `approval`=".($memory['unfreeze'] ? 0 : 1)." where `visitid`=\${id} and `approval`=".($memory['unfreeze'] ? 1 : 0),
			'check' => false,
			'errormsg' => 'Willingness Not Editable / No Change',
			'successmsg' => 'Willingness freezed successfully'
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>