<?php 
require_once(SBSERVICE);

/**
 *	@class RandomStringService
 *	@desc Generates random string with optionally provided length characterset
 *
 *	@param length integer String length [memory] optional default 10
 *	@param charset string Character set [memory] optional default 'qwert12yuiop34asdf56ghjkl78zxcv90bnm'
 *
 *	@return random string Result [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class RandomStringService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('length' => 10, 'charset' => 'qwert12yuiop34asdf56ghjkl78zxcv90bnm')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$length = $memory['length'];
		$charset = $memory['charset'];
		
		$result = '';
		$charsetlen = strlen($charset)-1;

		for($i = 0; $i < $length; $i++){
			$result .= $charset[mt_rand(0,$charsetlen)];
		}
	
		$memory['random'] = $result;
		$memory['valid'] = true;
		$memory['msg'] = 'Valid Random String Generation';
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