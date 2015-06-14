<?php 
require_once(SBSERVICE);

/**
 *	@class DataMapService
 *	@desc Maps data from arrays by keys
 *
 *	@param data array Array to map [memory] optional default array()
 *	@param mapkey string Map Key [memory] optional default 0
 *	@param mapname string Map Name [memory] optional default 'data'
 *
 *	@return result array Resulting array [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class DataMapService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('data' => array(), 'mapkey' => 0, 'mapname' => 'data')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$data = $memory['data'];
		$mapkey = $memory['mapkey'];
		$mapname = $memory['mapname'];
		$result = array();
		
		foreach($data as $row){
			$index = $row[$mapkey];
			if(isset($result[$index]))
				$result[$index][$mapname] = $row;
			else
				$result[$index] = array($mapname => $row);
		}
		
		
		$memory['result'] = $result;
		//$memory['result'] = array_values($result);
		$memory['valid'] = true;
		$memory['msg'] = 'Valid Data Merging';
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