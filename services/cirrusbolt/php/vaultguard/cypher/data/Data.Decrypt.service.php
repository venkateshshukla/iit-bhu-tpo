<?php 
require_once(SBSERVICE);
require_once('Data.Encrypt.service.php');

/**
 *	@class DataDEcryptService
 *	@desc Decrypts data using RC4 AES BLOWFISH TRIPLEDES modes
 *
 *	@param type string Secure type [memory] optional default 'rc4' ('rc4', 'aes', 'blowfish' 'tripledes', 'none')
 *	@param data string Data to be decrypted in base16 format [memory]
 *	@param key string Key used for decryption [memory]
 *
 *	@return result string Decrypted data [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class DataDecryptService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('data', 'key'),
			'optional' => array('type' => 'rc4')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$key = $memory['key'];
		$type = $key ? $memory['type'] : 'none';
		$data = $memory['data'];
		
		switch($type){
			case 'rc4' :
				$result = DataEncryptService::rc4(DataEncryptService::hex_decode($data), $key);
				break;
			case 'aes' :
				$result = $this->aes_decrypt($data, $key);
				break;
			case 'blowfish' :
				$result = $this->blowfish_decrypt($data, $key);
				break;
			case 'tripledes' :
				$result = $this->tripledes_decrypt($data, $key);
				break;
			case 'none' :
				$result = $data;
				break;
			default :
				$memory['valid'] = false;
				$memory['msg'] = 'Invalid Data Type';
				$memory['status'] = 501;
				$memory['details'] = 'Data decrypting not supported for type : '.$type.' @data.decrypt.service';
				return $memory;
		}
		
		if(($result === false || $result == null) && $type != 'none'){
			$memory['valid'] = false;
			$memory['msg'] = 'Invalid Encrypted Data';
			$memory['status'] = 501;
			$memory['details'] = 'Data could not be decrypted @data.decrypt.service';
			return $memory;
		}

		$memory['result'] = $result;
		$memory['valid'] = true;
		$memory['msg'] = 'Valid Data Decryption';
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
	
	public function aes_decrypt($data, $key){
		return 'Not implemented yet';
	}
	
	public function blowfish_decrypt($data, $key){
		return 'Not implemented yet';
	}
	
	public function tripledes_decrypt($data, $key){
		return 'Not implemented yet';
	}
	
}

?>