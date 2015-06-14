<?php 
require_once(SBSERVICE);

/**
 *	@class RandomNumberService
 *	@desc Generates random number between optionally provided limits
 *
 *	@param min integer Minimum limit [memory] optional
 *	@param max integer Maximum limit [memory] optional
 *
 *	@return random integer Result [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class RandomNumberService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('min' => false, 'max' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		if($memory['min'] && $memory['max']){
			$memory['random'] = mt_rand($memory['min'], $memory['max']);
		}
		else {
			$memory['random'] = mt_rand();
		}
	
		$memory['valid'] = true;
		$memory['msg'] = 'Valid Random Number Generation';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('random');
	}
	
}

?>