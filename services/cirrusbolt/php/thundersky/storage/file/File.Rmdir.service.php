<?php 
require_once(SBSERVICE);

/**
 *	@class FileRmdirService
 *	@desc Removes empty directory at specified destination
 *
 *	@param directory string Directory path [memory]
 *	@param exists boolean Delete if exists [memory] optional default true
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class FileRmdirService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('directory'),
			'optional' => array('exists' => true)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$directory = $memory['directory'];
		
		if(is_dir($directory)){
			if (!@rmdir($directory)){
				$memory['valid'] = false;
				$memory['msg'] = "Unable to Remove Directory / Non Empty Directory";
				$memory['status'] = 505;
				$memory['details'] = 'Error removing directory : '.$directory.' @file.rmdir.service';
				return $memory;
			}
		}
		else if(!$memory['exists']){
			$memory['valid'] = false;
			$memory['msg'] = "Directory Not Found";
			$memory['status'] = 504;
			$memory['details'] = 'Error directory not found : '.$file.' @file.rmdir.service';
			return $memory;
		}
		
		$memory['valid'] = true;
		$memory['msg'] = 'Directory Removed Successfully';
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