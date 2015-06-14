<?php 
require_once(SBSERVICE);

/**
 *	@class CompanyFindWorkflow
 *	@desc Returns company information by user
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Company User [memory]
 *	@param name string Company Name [memory] optional default user
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
 *	@return portalid long int Portal ID [memory]
 *	@return admin integer Is admin [memory]
 *	@return chain array Chain data [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class CompanyFindWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('user', 'keyid'),
			'optional' => array('portalid' => COMPANY_PORTAL_ID, 'name' => false),
			'set' => array('name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Company information given successfully';
		$memory['name'] = $memory['name'] ? $memory['name'] : $memory['user'];
		
		$workflow = array(
		array(
			'service' => 'people.person.find.workflow'
		),
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('pnid'),
			'conn' => 'exconn',
			'relation' => '`companies`',
			'sqlcnd' => "where `comid`=\${pnid}",
			'errormsg' => 'Invalid Company',
			'successmsg' => 'Company information given successfully'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result', 'chain'),
			'params' => array('result.0' => 'company', 'result.0.comid' => 'comid',  'company.folder' => 'folder', 'company.notes' => 'notes', 'chain.parent' => 'portalid'  /*'company.thumbnail' => 'thumbnail', 'company.username' => 'username'*/)
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid']) return $memory;
		
		$memory['id'] = $memory['comid'];
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('company', 'person', 'contact', 'personal', 'comid', 'id', 'folder', 'notes', /*'thumbnail', 'username',*/ 'dirid', 'portalid', 'admin', 'chain');
	}
	
}

?>