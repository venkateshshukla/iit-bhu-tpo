<?php 
require_once(SBSERVICE);

/**
 *	@class ContactAddWorkflow
 *	@desc Adds new contact
 *
 *	@param comname string Contact name [memory]
 *	@param comtype string Type [memory]
 *	@param cntinfo string Contacts [memory]
 *	@param incharge string Incharge username [memory]
 *	@param info string Information [memory]
 *	@param priority integer Priority [memory] optional default 
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param mngrid long int Manager ID [memory] optional default MANAGER_ID
 *	@param mngrname string Manager Name [memory] optional default ''
 *	@param level integer Web level [memory] optional default false (inherit manager admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
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
class ContactAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'comname', 'comtype', 'cntinfo', 'incharge', 'info'),
			'optional' => array('mngrid' => MANAGER_ID, 'mngrname' => '', 'priority' => 0, 'level' => false, 'owner' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['verb'] = 'added';
		$memory['join'] = 'on';
		$memory['public'] = 1;
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.add.workflow',
			'args' => array('comname', 'comtype', 'cntinfo', 'incharge', 'info', 'priority'),
			'input' => array('parent' => 'mngrid', 'cname' => 'comname', 'pname' => 'mngrname'),
			'conn' => 'exconn',
			'relation' => '`contacts`',
			'type' => 'contact',
			'sqlcnd' => "(`cntid`, `owner`, `priority`, `comname`, `comtype`, `cntinfo`, `incharge`, `info`) values (\${id}, \${owner}, \${priority}, '\${comname}', '\${comtype}', '\${cntinfo}', '\${incharge}', '\${info}')",
			'escparam' => array('comname', 'comtype', 'cntinfo', 'incharge', 'info'),
			'successmsg' => 'Contact added successfully',
			'output' => array('id' => 'cntid')
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