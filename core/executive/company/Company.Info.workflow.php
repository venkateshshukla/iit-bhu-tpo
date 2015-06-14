<?php 
require_once(SBSERVICE);

/**
 *	@class CompanyInfoWorkflow
 *	@desc Returns company information by ID
 *
 *	@param comid/id string Company ID [memory] optional default false
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string User name [memory] optional default ''
 *	@param portalid long int Portal ID [memory] optional default COMPANY_PORTAL_ID
 *
 *	@return company array Company information [memory]
 *	@return person array Person information [memory]
 *	@return contact array Company contact information [memory]
 *	@return personal array Company personal information [memory]
 *	@return comid long int Company ID [memory]
 *	@return name string Company name [memory]
 *	@return title string Company title [memory]
 *	@return resume long int Company resume ID [memory]
 *	@return thumbnail long int Company thumbnail ID [memory]
 *	@return home long int Company notes ID [memory]
 *	@return dirid long int Thumbnail Directory ID [memory]
 *	@return username string Company username [memory]
 *	@return portalid long int Batch ID [memory]
 *	@return admin integer Is admin [memory]
 *	@return chain array Chain data [memory]
 *	@return batch array Batch information [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class CompanyInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'comid' => false, 'portalid' => COMPANY_PORTAL_ID, 'id' => 0),
			'set' => array('id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Company information given successfully';
		$attr = $memory['comid'] ? 'comid' : ($memory['id'] ? 'comid' : 'owner');
		$memory['comid'] = $memory['comid'] ? $memory['comid'] : ($memory['id'] ? $memory['id'] : $memory['keyid']);
		
		// args arguments
		$memory['auth'] = isset($memory['auth']) ? $memory['auth'] : true;
		
		$workflow = array(
		array(
			'service' => 'people.person.info.workflow',
			'input' => array('pnid' => 'comid', 'peopleid' => 'portalid')
		),
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('comid'),
			'conn' => 'exconn',
			'relation' => '`companies`',
			'sqlcnd' => "where `$attr`=\${comid}",
			'errormsg' => 'Invalid Company ID',
			'successmsg' => 'Company information given successfully'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result', 'chain'),
			'params' => array('result.0' => 'company', 'result.0.comid' => 'comid',  'company.folder' => 'folder', 'company.notes' => 'notes', 'chain.parent' => 'portalid' /*'company.thumbnail' => 'thumbnail', 'company.username' => 'username'*/)
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('company', 'person', 'contact', 'personal', 'comid', 'folder', 'notes', /* 'thumbnail', 'username',*/ 'dirid', 'portalid', 'admin', 'chain');
	}
	
}

?>
