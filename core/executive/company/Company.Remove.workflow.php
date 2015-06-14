<?php 
require_once(SBSERVICE);

/**
 *	@class CompanyRemoveWorkflow
 *	@desc Removes company by ID
 *
 *	@param comid long int Company ID [memory]
 *	@param portalid long int Portal ID [memory] optional default COMPANY_PORTAL_ID
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class CompanyRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'comid'),
			'optional' => array('portalid' => COMPANY_PORTAL_ID)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Company removed successfully';
		
		$workflow = array(
		array(
			'service' => 'executive.company.info.workflow'
		),
		array(
			'service' => 'people.person.remove.workflow',
			'input' => array('pnid' => 'comid', 'peopleid' => 'portalid')
		),
		array(
			'service' => 'transpera.relation.delete.workflow',
			'args' => array('comid'),
			'conn' => 'exconn',
			'relation' => '`companies`',
			'sqlcnd' => "where `comid`=\${comid}",
			'errormsg' => 'Invalid Company ID'
		),
		array(
			'service' => 'display.board.remove.workflow',
			'input' => array('boardid' => 'notes', 'forumid' => 'portalid')
		),
		array(
			'service' => 'storage.directory.remove.workflow',
			'input' => array('dirid' => 'folder', 'stgid' => 'portalid')
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