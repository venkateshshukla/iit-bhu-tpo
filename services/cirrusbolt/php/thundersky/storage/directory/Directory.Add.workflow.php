<?php 
require_once(SBSERVICE);

/**
 *	@class DirectoryAddWorkflow
 *	@desc Adds new directory to storage
 *
 *	@param name string Directory name [memory]
 *	@param path string Directory path [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param stgid long int Storage ID [memory] optional default 0
 *	@param level integer Web level [memory] optional default 1 (storage admin access allowed)
 *	@param grlevel integer Group level [memory] optional default 0
 *	@param grroot long int Group root [memory] optional default (inherit) 0
 *	@param owner long int Owner Key ID [memory] optional default keyid
 *
 *	@return dirid long int Directory ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class DirectoryAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'name', 'path'),
			'optional' => array('stgid' => 0, 'stgname' => '', 'level' => 1, 'grlevel' => 0, 'grroot' => 0, 'owner' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['owner'] = $memory['owner'] ? $memory['owner'] : $memory['keyid'];
		$memory['msg'] = 'Directory added successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.add.workflow',
			'args' => array('name', 'path'),
			'input' => array('parent' => 'stgid'),
			'conn' => 'cbsconn',
			'relation' => '`directories`',
			'type' => 'directory',
			'sqlcnd' => "(`dirid`, `owner`, `name`, `path`) values (\${id}, \${owner}, '\${name}', '\${path}')",
			'escparam' => array('name', 'path'),
			'successmsg' => 'Directory added successfully',
			'output' => array('id' => 'dirid'),
			'construct' => array(
				array(
					'service' => 'storage.file.mkdir.service',
					'input' => array('directory' => 'path')
				)
			),
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
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('dirid', 'stgid', 'stgname', 'directory', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>