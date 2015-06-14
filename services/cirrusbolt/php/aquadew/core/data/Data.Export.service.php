<?php 
require_once(SBSERVICE);

/**
 *	@class DataExportService
 *	@desc Exports all values in matrix into CSV formats
 *
 *	@param data array Input matrix [memory]
 *	@param type string Export type [memory] optional default 'csv' ('csv')
 *	@param default string Export header [memory] optional default ''
 *	@param filepath string Filepath [memory] optional default 'storage/private/exports/'
 *	@param filename string Filename [memory]
 *
 *	@return result string Resultant string [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class DataExportService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('data', 'filename'),
			'optional' => array('type' => 'csv', 'default' => '', 'filepath' => 'storage/private/exports/')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$data = $memory['data'];
		$type = $memory['type'];
		$result = $memory['default'];
		ini_set("auto_detect_line_endings", true);
		
		switch($type){
			case 'csv' :
				$fh = fopen($memory['filepath'].$memory['filename'], 'w');
				fwrite($fh, $result);
				foreach($data as $tuple){
					fputcsv($fh, $tuple);
					//$result .= implode(',', $tuple);
					//$result .= "\r\n";
				}
				//fwrite($fh, $result);
				fclose($fh);
				break;
			default :
				$memory['valid'] = false;
				$memory['msg'] = 'Invalid Export Type';
				$memory['status'] = 505;
				$memory['details'] = 'Export not supported for type : '.$type.' @data.export.service';
				return $memory;
		}

		$memory['result'] = $result;
		$memory['valid'] = true;
		$memory['msg'] = 'Valid List Construction';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('result');
	}
	
}

?>