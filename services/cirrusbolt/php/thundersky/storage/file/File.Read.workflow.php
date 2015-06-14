<?php 
require_once(SBSERVICE);

/**
 *	@class FileReadWorkflow
 *	@desc Reads file information and downloads file by ID
 *
 *	@param fileid long int File ID [memory]
 *	@param dirid long int Directory ID [memory] optional default 0
 *	@param asname string As name [memory] optional default false
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class FileReadWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('fileid', 'keyid'),
			'optional' => array('dirid' => false, 'asname' => false),
			'set' => array('fileid', 'dirid')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		if($memory['dirid'])
			$attr = 'dirid_old';
		else
			$attr = 'dirid';
			//Snowblozm::$debug = true;
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'action' => 'info',
			'input' => array('id' => 'fileid')
		),
		array(
			'service' => 'storage.file.info.workflow',
			'output' => array('name' => 'asname', 'parent' => $attr)
		),
		array(
			'service' => 'storage.directory.info.workflow',
			'output' => array('path' => 'filepath')
		),
		array(
			'service' => 'storage.file.download.service'
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