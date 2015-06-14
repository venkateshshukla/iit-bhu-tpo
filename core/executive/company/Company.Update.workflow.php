<?php 
require_once(SBSERVICE);

/**
 *	@class CompanyUpdateWorkflow
 *	@desc Updates company using ID
 *
 *	@param comid long int Company ID [memory]
 *	@param scope array Scope [memory]
 *	@param site string Website [memory]
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Username [memory]
 *	@param portalid long int Batch ID [memory] optional default 0
 *	@param plname string Batch Name [memory] optional default ''
 *
 *	@return comid long int Company ID [memory]
 *	@return portalid long int Batch ID [memory]
 *	@return plname string Batch Name [memory]
 *	@return company array Company information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class CompanyUpdateWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'comid', 'scope', 'site', 'portalid', 'plname')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['scope'] = is_array($memory['scope']) ? implode(':', $memory['scope']) : $memory['scope'];
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'comid'),
			'init' => false
		),
		array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('comid', 'scope', 'site'),
			'conn' => 'exconn',
			'relation' => '`companies`',
			'sqlcnd' => "set `scope`='\${scope}', `site`='\${site}' where `comid`=\${comid}",
			'successmsg' => 'Company updated successfully',
			'check' => false,
			'escparam' => array('scope', 'site'),
			'errormsg' => 'No Change / Invalid Company ID'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'comid', 'parent' => 'portalid', 'cname' => 'name', 'pname' => 'plname'),
			'conn' => 'exconn',
			'relation' => '`companies`',
			'sqlprj' => '`comid`, `username`, `name`, `email`, `scope`, `folder`, `notes`, `site`',
			'sqlcnd' => "where `comid`=\${id}",
			'errormsg' => 'Invalid Company ID',
			'type' => 'person',
			'successmsg' => 'Company information given successfully',
			'output' => array('entity' => 'company'),
			'auth' => false,
			'track' => false,
			'sinit' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'portalid'),
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
		return array('comid', 'portalid', 'plname', 'company', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>