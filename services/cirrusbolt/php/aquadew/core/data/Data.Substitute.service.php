<?php 
require_once(SBSERVICE);

/**
 *	@class DataSubstituteService
 *	@desc Substitutes ${key} in base string with value from memory for all keys in params array
 *
 *	@param args array Array of key to use for substitutions [args]
 *	@param data string Base string to use for substitutions [memory]
 *
 *	@return result string String with substitutions done [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class DataSubstituteService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('data')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$data = $memory['data'];
		$args = $memory['args'];
		
		foreach($args as $key){
			if(!isset($memory[$key])){
				$memory['valid'] = false;
				$memory['msg'] = 'Invalid Service State';
				$memory['status'] = 504;
				$memory['details'] = 'Value not found for '.$key.' @data.substitute.service';
				return $memory;
			}
			$data = str_replace('${'.$key.'}', $memory[$key], $data);
		}

		$memory['result'] = $data;
		$memory['valid'] = true;
		$memory['msg'] = 'Valid Data Substitution';
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