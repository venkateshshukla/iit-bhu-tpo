<?php 
require_once(SBSERVICE);

/**
 *	@class FileRemoveWorkflow
 *	@desc Removes file by ID
 *
 *	@param fileid long int File ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param dirid long int Directory ID [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class FileRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'fileid'),
			'optional' => array('dirid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'File removed successfully';
		
		$workflow = array(
		array(
			'service' => 'storage.directory.info.workflow',
			'output' => array('path' => 'filepath')
		),
		array(
			'service' => 'storage.file.info.workflow'
		),
		array(
			'service' => 'transpera.reference.remove.workflow',
			'input' => array('parent' => 'dirid', 'id' => 'fileid'),
			'type' => 'file'
		),
		array(
			'service' => 'transpera.relation.delete.workflow',
			'args' => array('fileid'),
			'conn' => 'cbsconn',
			'relation' => '`files`',
			'type' => 'file',
			'sqlcnd' => "where `fileid`=\${fileid}",
			'errormsg' => 'Invalid File ID'
		),
		array(
			'service' => 'storage.file.unlink.service'
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