<?php 
require_once(SBSERVICE);

/**
 *	@class FileArchiveService
 *	@desc Reads and saves zipped file at specified destination
 *
 *	@param directory string Directory path [memory]
 *	@param filename string Filename [memory] optional default 'archive.zip'
 *	@param filelist array File list [memory] optional default false
 *
 *	@return size long int File size in bytes [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class FileArchiveService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('directory'),
			'optional' => array('filename' => 'archive.zip', 'filelist' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$directory = $memory['directory'];
		$archive = $memory['filename'];
		
		if (is_dir($directory)) {
			//ini_set("max_execution_time", 300);

			$zip = new ZipArchive();
			if ($zip->open($directory.$archive, ZIPARCHIVE::OVERWRITE ) !== true) {
				$memory['valid'] = false;
				$memory['msg'] = "Could not create archive";
				$memory['status'] = 505;
				$memory['details'] = 'Error creating archive @file.archive.service';
				return $memory;
			}
			
			if(is_array($memory['filelist'])){
				foreach($memory['filelist'] as $filedesc){
					if(file_exists($filedesc['filepath'].$filedesc['filename'])){
						if($zip->addFile($filedesc['filepath'].$filedesc['filename'], $filedesc['asname']) !== true){
							$memory['valid'] = false;
							$memory['msg'] = "Unable to add file";
							$memory['status'] = 504;
							$memory['details'] = 'Error adding file : '.$key.' @file.archive.service';
							return $memory;
						}
					}
				}
			}
			else {
				$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
				foreach ($iterator as $key => $value) {
					$realpath = realpath($key);
					if($realpath == realpath($directory.$archive)) continue;
					
					if($zip->addFile($realpath, $key) !== true){
						$memory['valid'] = false;
						$memory['msg'] = "Unable to add file";
						$memory['status'] = 504;
						$memory['details'] = 'Error adding file : '.$key.' @file.archive.service';
						return $memory;
					}
				}
			}
			
			$zip->close();
		}
		else {
			$memory['valid'] = false;
			$memory['msg'] = "Directory Not Found";
			$memory['status'] = 504;
			$memory['details'] = 'Error file not found : '.$file.' @file.archive.service';
			return $memory;
		}
		
		$stat = @stat($directory.$archive);
		$memory['size'] = isset($stat['size']) ? $stat['size'] : 0;
		$memory['valid'] = true;
		$memory['msg'] = 'File Archived Successfully';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('size');
	}
	
}

?>