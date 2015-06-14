<?php 
require_once(SBSERVICE);

/**
 *	@class DirectoryInfoWorkflow
 *	@desc Returns information for directory using ID
 *
 *	@param dirid long int Directory ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param stgid long int Storage ID [memory] optional default 0
 *	@param stgname string Storage name [memory] optional default ''
 *
 *	@return dirid string Directory ID [memory]
 *	@return stgname string Storage name [memory]
 *	@return stgid long int Storage ID [memory]
 *	@return name string Directory name [memory]
 *	@return path string Directory path [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class DirectoryInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('dirid'),
			'optional' => array('keyid' => false, 'stgname' => '', 'stgid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Directory information given successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'dirid', 'parent' => 'stgid'),
			'conn' => 'cbsconn',
			'relation' => '`directories`',
			'sqlcnd' => "where `dirid`=\${id}",
			'errormsg' => 'Invalid Directory ID',
			'output' => array('entity' => 'directory')
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('directory'),
			'params' => array('directory.name' => 'name', 'directory.path' => 'path')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('dirid', 'directory', 'stgid', 'stgname', 'name', 'path');
	}
	
}

?>