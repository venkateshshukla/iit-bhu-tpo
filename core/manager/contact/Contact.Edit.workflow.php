<?php 
require_once(SBSERVICE);

/**
 *	@class ContactEditWorkflow
 *	@desc Edits contact using ID
 *
 *	@param cntid long int Contact ID [memory]
 *	@param comname string Contact name [memory]
 *	@param comtype string Type [memory]
 *	@param cntinfo string Contacts [memory]
 *	@param incharge string Incharge username [memory]
 *	@param info string Information [memory]
 *	@param priority integer Priority [memory] optional default 0
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param mngrid long int Manager ID [memory] optional default MANAGER_ID
 *	@param mngrname string Manager Name [memory] optional default ''
 *
 *	@return cntid long int Contact ID [memory]
 *	@return mngrid long int Manager ID [memory]
 *	@return mngrname string Manager Name [memory]
 *	@return contact array Contact information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ContactEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'cntid', 'comname', 'comtype', 'cntinfo', 'incharge', 'info', 'priority'),
			'optional' => array('mngrid' => MANAGER_ID, 'mngrname' => '')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){		
		$memory['public'] = 1;
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('comname', 'comtype', 'cntinfo', 'incharge', 'info', 'priority'),
			'input' => array('id' => 'cntid', 'cname' => 'comname', 'parent' => 'mngrid', 'pname' => 'mngrname'),
			'conn' => 'exconn',
			'relation' => '`contacts`',
			'type' => 'contact',
			'sqlcnd' => "set `comname`='\${comname}', `comtype`='\${comtype}', `cntinfo`='\${cntinfo}', `incharge`='\${incharge}', `info`='\${info}', `priority`='\${priority}' where `cntid`=\${id}",
			'escparam' => array('comname', 'comtype', 'cntinfo', 'incharge', 'info'),
			'check' => false,
			'successmsg' => 'Contact edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'cntid', 'parent' => 'mngrid', 'cname' => 'comname', 'pname' => 'mngrname'),
			'conn' => 'exconn',
			'relation' => '`contacts`',
			'sqlcnd' => "where `cntid`=\${id}",
			'errormsg' => 'Invalid Contact ID',
			'type' => 'contact',
			'successmsg' => 'Contact information given successfully',
			'output' => array('entity' => 'contact'),
			'auth' => false,
			'track' => false,
			'sinit' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'mngrid'),
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
		return array('cntid', 'mngrid', 'mngrname', 'contact', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>