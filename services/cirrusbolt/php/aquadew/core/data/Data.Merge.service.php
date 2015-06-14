<?php 
require_once(SBSERVICE);

/**
 *	@class DataMergeService
 *	@desc Merges data from multiple arrays into single one by key
 *
 *	@param params array Array indicating data to be merged [memory] optional default array('data' => array(0, 'data'))
 *
 *	@return result array Resulting array [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class DataMergeService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('params' => array('data' => array(0, 'data')))
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$params = $memory['params'];
		$result = array();
		$flag = true;
		
		foreach($params as $value => $keys){
			$data = $memory[$value];
			$key = $keys[0];
			$val = $keys[1];
			
			foreach($data as $id => $row){
				if(isset($result[$id]))
					$result[$id] = array_merge($result[$id], $row);
				elseif($flag)
					$result[$id] = $row;
			}
			
			$flag = false;
		}

		//$memory['result'] = $result;
		$memory['result'] = array_values($result);
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