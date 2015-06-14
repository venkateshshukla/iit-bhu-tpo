<?php 
require_once(SBSERVICE);

/**
 *	@class DataListService
 *	@desc Lists all values as set for use in query
 *
 *	@param args[0] string Key to resultset as array [args]
 *	@param attr string/integer Key to use for accessing value to be included in list [memory] optional default 0
 *	@param default array Initialize array for result [memory] optinal default array()
 *	@param mapname string Map Name [memory] optional default false
 *
 *	@return list string Resultant list as string [memory]
 *	@return result array Resultant array [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class DataListService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('attr' => 0, 'default' => array())
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$args = $memory['args'];
		$attr = $memory['attr'];
		$mapname = $memory['mapname'];
		$list = $memory['default'];
		
		if(isset($args[0])){
			$key = $args[0];
			if(!isset($memory[$key])){
				$memory['valid'] = false;
				$memory['msg'] = 'Invalid Service State';
				$memory['status'] = 505;
				$memory['details'] = 'Value not found for key : '.$key.' @data.list.service';
				return $memory;
			}
			
			$resultset = $memory[$key];
			foreach($resultset as $key => $tuple){
				array_push($list, $mapname ? $tuple[$mapname][$attr] : $tuple[$attr]);
			}
		}

		$memory['list'] = '('.implode(',', $list).')';
		$memory['result'] = $list;
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
		return array('list', 'result');
	}
	
}

?>