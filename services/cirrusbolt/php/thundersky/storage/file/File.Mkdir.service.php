<?php 
require_once(SBSERVICE);

/**
 *	@class FileMkdirService
 *	@desc Creates directory at specified destination
 *
 *	@param directory string Directory path [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class FileMkdirService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('directory')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$directory = $memory['directory'];
		
		if (!file_exists($directory) && !@mkdir($directory, 0777, true)){
			$memory['valid'] = false;
			$memory['msg'] = "Unable to Make Directory";
			$memory['status'] = 505;
			$memory['details'] = 'Error creating directory : '.$directory.' @file.mkdir.service';
			return $memory;
		}
		
		$memory['valid'] = true;
		$memory['msg'] = 'Directory Made Successfully';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>