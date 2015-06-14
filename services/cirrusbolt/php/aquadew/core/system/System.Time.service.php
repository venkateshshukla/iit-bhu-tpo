<?php 
require_once(SBSERVICE);

/**
 *	@class SystemTimeService
 *	@desc Returns current time both timestamp and formatted
 *
 *	@param diff long int Time difference [memory] optional default 0
 *
 *	@return timestamp long int Timestamp [memory]
 *	@return formatted string Formatted time [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class SystemTimeService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('diff' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$diff = $memory['diff'];
		
		$memory['timestamp'] = time() + $diff;
		$memory['formatted'] = date('c', $memory['timestamp']);
		
		$memory['valid'] = true;
		$memory['msg'] = 'Valid Time Given';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('timestamp', 'formatted');
	}
	
}

?>