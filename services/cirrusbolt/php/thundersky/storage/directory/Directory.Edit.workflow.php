<?php 
require_once(SBSERVICE);

/**
 *	@class DirectoryEditWorkflow
 *	@desc Edits directory of storage
 *
 *	@param dirid long int Directory ID [memory]
 *	@param name string Directory name [memory]
 *	@param path string Directory path [memory]
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class DirectoryEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'dirid', 'name', 'path', 'stgid', 'stgname')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$workflow = array(
		array(
			'service' => 'transpera.entity.edit.workflow',
			'input' => array('id' => 'dirid'),
			'args' => array('name', 'path'),
			'conn' => 'cbsconn',
			'relation' => '`directories`',
			'sqlcnd' => "set `name`='\${name}', `path`='\${path}' where `dirid`=\${id}",
			'escparam' => array('name', 'path'),
			'check' => false,
			'successmsg' => 'Directory edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'dirid', 'parent' => 'stgid', 'cname' => 'name', 'pname' => 'stgname'),
			'conn' => 'cbsconn',
			'relation' => '`directories`',
			'type' => 'directory',
			'sqlprj' => '`dirid`, `owner`, `name`, `path`',
			'sqlcnd' => "where `dirid`=\${id}",
			'errormsg' => 'Invalid Directory ID',
			'successmsg' => 'Directory information given successfully',
			'output' => array('entity' => 'directory'),
			'auth' => false,
			'track' => false,
			'sinit' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'stgid'),
			'output' => array('chain' => 'pchain')
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
		
		$memory['padmin'] = $memory['admin'];
		$memory['admin'] = 1;
		return $memory;
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('dirid', 'stgid', 'stgname', 'directory', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>