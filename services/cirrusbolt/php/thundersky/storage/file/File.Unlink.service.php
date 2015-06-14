<?php 
require_once(SBSERVICE);

/**
 *	@class FileUnlinkService
 *	@desc Deletes file at specified destination
 *
 *	@param filepath string Filepath [memory]
 *	@param filename string Filename [memory]
 *	@param exists boolean Delete if exists [memory] optional default true
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class FileUnlinkService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('filepath', 'filename'),
			'optional' => array('exists' => true)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$file = $memory['filepath'].$memory['filename'];
		
		if(file_exists($file)){
			if (!@unlink($file)){
				$memory['valid'] = false;
				$memory['msg'] = "Unable to Delete File";
				$memory['status'] = 505;
				$memory['details'] = 'Error deleting file : '.$file.' @file.unlnk.service';
				return $memory;
			}
		}
		else if(!$memory['exists']){
			$memory['valid'] = false;
			$memory['msg'] = "File Not Found";
			$memory['status'] = 504;
			$memory['details'] = 'Error file not found : '.$file.' @file.unlnk.service';
			return $memory;
		}
		
		$memory['valid'] = true;
		$memory['msg'] = 'File Deleted Successfully';
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