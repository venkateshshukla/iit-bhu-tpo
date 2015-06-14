<?php 
require_once(SBSERVICE);

/**
 *	@class DirectoryRemoveWorkflow
 *	@desc Removes directory by ID
 *
 *	@param dirid long int Directory ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param stgid long int Storage ID [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class DirectoryRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'dirid'),
			'optional' => array('stgid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Directory removed successfully';
		
		$workflow = array(
		array(
			'service' => 'storage.directory.info.workflow'
		),
		array(
			'service' => 'transpera.reference.remove.workflow',
			'input' => array('parent' => 'stgid', 'id' => 'dirid'),
			'type' => 'directory'
		),
		array(
			'service' => 'transpera.relation.delete.workflow',
			'args' => array('dirid'),
			'conn' => 'cbsconn',
			'relation' => '`directories`',
			'sqlcnd' => "where `dirid`=\${dirid}",
			'errormsg' => 'Invalid Directory ID'
		),
		array(
			'service' => 'storage.file.unlink.service',
			'input' => array('filepath' => 'path'),
			'filename' => 'archive.zip'
		),
		array(
			'service' => 'storage.file.rmdir.service',
			'input' => array('directory' => 'path')
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