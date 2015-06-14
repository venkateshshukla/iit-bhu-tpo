<?php 
require_once(SBSERVICE);

/**
 *	@class VisitRemoveWorkflow
 *	@desc Removes visit by ID
 *
 *	@param visitid long int Visit ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 
 *	@param portalid long int Portal ID [memory] optional default COMPANY_PORTAL_ID
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class VisitRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'visitid'),
			'optional' => array('portalid' => COMPANY_PORTAL_ID)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$workflow = array(
		array(
			'service' => 'executive.visit.info.workflow'
		),
		array(
			'service' => 'transpera.entity.remove.workflow',
			'input' => array('id' => 'visitid', 'parent' => 'portalid'),
			'conn' => 'exconn',
			'relation' => '`willingnesses`',
			'type' => 'willingness',
			'sqlcnd' => "where `visitid`=\${id}",
			'errormsg' => 'Invalid Visit ID',
			'successmsg' => 'Willingnesses removed successfully'
		),
		array(
			'service' => 'transpera.entity.remove.workflow',
			'args' => array('files', 'shortlist', 'comid'),
			'input' => array('id' => 'visitid', 'parent' => 'portalid'),
			'conn' => 'exconn',
			'relation' => '`visits`',
			'type' => 'visit',
			'sqlcnd' => "where `visitid`=\${id}",
			'errormsg' => 'Invalid Visit ID',
			'destruct' => array(
				array(
					'service' => 'storage.directory.remove.workflow',
					'input' => array('dirid' => 'files', 'stgid' => 'comid')
				),
				array(
					'service' => 'transpera.reference.remove.workflow',
					'input' => array('id' => 'shortlist'),
					'type' => 'shortlist'
				)
			),
			'successmsg' => 'Visit removed successfully'
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