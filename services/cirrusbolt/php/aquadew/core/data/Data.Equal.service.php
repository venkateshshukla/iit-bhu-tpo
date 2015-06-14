<?php 
require_once(SBSERVICE);

/**
 *	@class DataEqualService
 *	@desc Checks for equality and gives error message as configured
 *
 *	@param data mixed Data to be checked [memory] optional default 1
 *	@param value mixed Value to check against [memory] optional default 1
 *	@param not boolean Is error on non-equalilty [memory] optional default true
 *	@param errormsg string Error message [memory]
 *	@param errstatus integer Error status code [memory] optional default 505
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class DataEqualService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('errormsg'),
			'optional' => array('data' => 1, 'value' => 1, 'not' => true, 'errstatus' => 505)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$data = $memory['data'];
		$value = $memory['value'];
		$not = $memory['not'];
		$errormsg = $memory['errormsg'];
		$errstatus = $memory['errstatus'];
		
		if($not ^ ($data == $value)){
			$memory['valid'] = false;
			$memory['msg'] = $errormsg;
			$memory['status'] = $errstatus;
			$memory['details'] = 'Data not equal to value : '.$value.' @data.equal.service';
			return $memory;
		}

		$memory['valid'] = true;
		$memory['msg'] = 'Valid Equality Check';
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