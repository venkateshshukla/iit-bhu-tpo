<?php 
require_once(SBSERVICE);

/**
 *	@class FileDownloadService
 *	@desc Reads and echoes file at specified destination
 *
 *	@param filepath string Filepath [memory]
 *	@param filename string Filename [memory]
 *	@param size long int File size in bytes [memory]
 *	@param mime string File MIME type [message] optional default 'application/force-download'
 *	@param asname string Download filename [memory] optional default 'filename'
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class FileDownloadService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('filepath', 'filename'),
			'optional' => array('mime' => 'application/force-download', 'asname' => false, 'size' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$file = $memory['filepath'].$memory['filename'];
		$asname = $memory['asname'] ? $memory['asname'] : $memory['filename'];
		$mime = $memory['mime'];
		
		if (file_exists($file)) {
			if($memory['size'])
				$size = $memory['size'];
			else {
				$stat = stat($file);
				$size = $stat['size'];
			}
			
			set_time_limit(0);
			header('Content-Description: File Transfer');
			header("Content-Type: $mime");
			header("Content-Disposition: attachment; filename=\"$asname\"");
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . $size);
			ob_clean();
			flush();
			readfile($file);
			exit;
		}
		else {
				header("HTTP/1.0 404 Not Found"); 
				echo "<h1>Error 404 : File Not Found</h1>"; 
				exit;
				
				$memory['valid'] = false;
				$memory['msg'] = "File Not Found";
				$memory['status'] = 504;
				$memory['details'] = 'Error file not found : '.$file.' @file.download.service';
				return $memory;
		}
		
		$memory['valid'] = true;
		$memory['msg'] = 'File Downloaded Successfully';
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