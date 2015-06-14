<?php 
require_once(SBSERVICE);

/**
 *	@class DataHashService
 *	@desc Hashes data using MD5 SHA1 CRC32 modes
 *
 *	@param type string Hash type [memory] optional default 'md5' ('md5', 'sha1', 'crc32', 'none')
 *	@param data string Data to be encoded [memory]
 *
 *	@return result string Hashed data [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class DataHashService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('data'),
			'optional' => array('type' => 'md5')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$type = $memory['type'];
		$data = $memory['data'];
		
		switch($type){
			case 'md5' :
				$result = md5($data);
				break;
			case 'sha1' :
				$result = sha1($data);
				break;
			case 'crc32' :
				$result = crc32($data);
				break;
			case 'none' :
				$result = false;
				break;
			default :
				$memory['valid'] = false;
				$memory['msg'] = 'Invalid Data Type';
				$memory['status'] = 501;
				$memory['details'] = 'Data hashing not supported for type : '.$type.' @data.hash.service';
				return $memory;
		}
		
		if($result === false && $type != 'none'){
			$memory['valid'] = false;
			$memory['msg'] = 'Invalid Data';
			$memory['status'] = 501;
			$memory['details'] = 'Data could not be hashed @data.hash.service';
			return $memory;
		}

		$memory['result'] = $result;
		$memory['valid'] = true;
		$memory['msg'] = 'Valid Data Hash';
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