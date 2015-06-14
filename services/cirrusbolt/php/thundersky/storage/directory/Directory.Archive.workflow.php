<?php 
require_once(SBSERVICE);

/**
 *	@class DirectoryArchiveWorkflow
 *	@desc Archives directory storages and downloads it
 *
 *	@param dirid long int Directory ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param asname string As name [memory] optional default 'archive.zip'
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class DirectoryArchiveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'dirid'),
			'optional' => array('asname' => 'archive.zip')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Directory archived successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'dirid'),
			'action' => 'edit'
		),
		array(
			'service' => 'storage.directory.info.workflow'
		),
		array(
			'service' => 'storage.file.archive.service',
			'input' => array('directory' => 'path')
		),
		array(
			'service' => 'storage.file.download.service',
			'input' => array('filepath' => 'path'),
			'filename' => 'archive.zip',
			'mime' => 'application/zip'
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