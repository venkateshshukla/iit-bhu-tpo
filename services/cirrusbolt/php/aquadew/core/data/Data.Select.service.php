<?php 
require_once(SBSERVICE);

/**
 *	@class DataSelectService
 *	@desc Selects data from deeper arrays into memory
 *
 *	@param params array Array indicating data to be selected [memory] optional default array()
 *	@param opt boolean Is optional [memory] optional default false
 *	@param errormsg string Error message [memory] optional default 'Invalid Data Selection'
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class DataSelectService implements Service {
	
	/**
	 *	@var output
	**/
	private $output;
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('params' => array(), 'errormsg' => 'Invalid Data Selection', 'opt' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$params = $memory['params'];
		$errormsg = $memory['errormsg'];
		$opt = $memory['opt'];
		$this->output = array();
		
		foreach($params as $key => $value){
			$tokens = explode('.', $key);
			$result = $memory;
			$len=count($tokens);
			
			for($i=0; $i<$len; $i++){
				if(!isset($result[$tokens[$i]])){
					if($opt){
						$result = false;
						break;
					}
						
					$memory['valid'] = false;
					$memory['msg'] = $errormsg;
					$memory['status'] = 505;
					$memory['details'] = 'Value not found for token : '.$tokens[$i].' @data.select.service';
					return $memory;
				}
				$result = $result[$tokens[$i]];
			}
			
			if($result !== false){
				$memory[$value] = $result;
				array_push($this->output, $value);
			}
		}

		$memory['valid'] = true;
		$memory['msg'] = 'Valid Data Selection';
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