<?php 
require_once(SBSERVICE);

/**
 *	@class DataNumericService
 *	@desc Checks all values for being number
 *
 *	@param args array Array of numbers to check [args]
 *	@param errormsg string Error message [memory] optional default 'Invalid Numeric Value'
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class DataNumericService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('errormsg' => 'Invalid Numeric Value')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$args = $memory['args'];
		$errormsg = $memory['errormsg'];
		
		foreach($args as $key){
			if(is_array($memory[$key])){
				foreach($memory[$key] as $val){
					if(!is_numeric($val)){
						$memory['valid'] = false;
						$memory['msg'] = $errormsg;
						$memory['status'] = 505;
						$memory['details'] = 'Value not numeric : '.$key.' = '.$val.' @data.numeric.service';
						return $memory;
					}
				}
			}
			elseif(!is_numeric($memory[$key])){
				$memory['valid'] = false;
				$memory['msg'] = $errormsg;
				$memory['status'] = 505;
				$memory['details'] = 'Value not numeric : '.$key.' = '.$memory[$key].' @data.numeric.service';
				return $memory;
			}
		}

		$memory['valid'] = true;
		$memory['msg'] = 'Valid Number Checks';
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