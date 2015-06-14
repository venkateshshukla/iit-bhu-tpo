<?php 
require_once(SBSERVICE);
require_once(CBMYSQL);

/**
 *	@class QueryEscapeService
 *	@desc Escapes all strings in the array
 *
 *	@param args array Array of strings to escape [args]
 *	@param conn resource DataService instance [memory]
 *
 *	@return result values as args values [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class QueryEscapeService implements Service {
	
	/**
	 *	@var output
	**/
	private $output;
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('conn')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$conn = $memory['conn'];
		$this->output = $params = $memory['args'];
		
		foreach($params as $key){
			if(is_array($memory[$key])){
				foreach($memory[$key] as $val){
					$val = $conn->escape($val);
				}
			}
			else
				$memory[$key] = $conn->escape($memory[$key]);
		}

		$memory['valid'] = true;
		$memory['msg'] = 'Valid Escapes';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return $this->output;
	}
	
}

?>