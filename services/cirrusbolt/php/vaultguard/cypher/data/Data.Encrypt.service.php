<?php 
require_once(SBSERVICE);

/**
 *	@class DataEncryptService
 *	@desc Encrypts data using RC4 AES BLOWFISH TRIPLEDES modes
 *
 *	@param type string Secure type [memory] optional default 'rc4' ('rc4', 'aes', 'blowfish' 'tripledes', 'none')
 *	@param data string Data to be encrypted [memory]
 *	@param key string Key used for encryption [memory] optional (defaults type to none if key not set)
 *
 *	@return result string Encrypted data in base16 format [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class DataEncryptService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('data'),
			'optional' => array('type' => 'rc4', 'key' => false)
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
				$result = self::hex_encode(self::rc4($data, $key));
				break;
			case 'aes' :
				$result = $this->aes_encrypt($data, $key);
				break;
			case 'blowfish' :
				$result = $this->blowfish_encrypt($data, $key);
				break;
			case 'tripledes' :
				$result = $this->tripledes_encrypt($data, $key);
				break;
			case 'none' :
				$result = $data;
				break;
			default :
				$memory['valid'] = false;
				$memory['msg'] = 'Invalid Data Type';
				$memory['status'] = 501;
				$memory['details'] = 'Data encrypting not supported for type : '.$type.' @data.encrypt.service';
				return $memory;
		}
		
		if($result === false || $result == null){
			$memory['valid'] = false;
			$memory['msg'] = 'Invalid Data for Encryption';
			$memory['status'] = 501;
			$memory['details'] = 'Data could not be encrypted @data.encrypt.service';
			return $memory;
		}

		$memory['result'] = $result;
		$memory['valid'] = true;
		$memory['msg'] = 'Valid Data Encryption';
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
	
	public static function hex_encode($data){			
		$result='';
		for ($i=0; isset($data[$i]); $i++){
			$result .= str_pad(dechex(ord($data[$i])), 2, '0', STR_PAD_LEFT);
		}
		
		return $result;
	}
	
	public static function hex_decode($data){
		$result='';
		for ($i=0; isset($data[$i]); $i+=2){
			$hexChar = substr($data,$i,2);
			$result .= chr(hexdec($hexChar));
		}
		return $result;
	}
	
	public static function rc4($data, $key){
		$stream = array();
		for ($i=0; $i<256; $i++) {
			$stream[$i] = $i;
		}
		$j = 0;
		$x;
		for ($i=0; $i<256; $i++) {
			$j = ($j + $stream[$i] + ord($key[$i % strlen($key)])) % 256;
			$x = $stream[$i];
			$stream[$i] = $stream[$j];
			$stream[$j] = $x;
		}
		$i = 0;
		$j = 0;
		$result = '';
		for ($k=0; $k<strlen($data); $k++) {
			$i = ($i + 1) % 256;
			$j = ($j + $stream[$i]) % 256;
			$x = $stream[$i];
			$stream[$i] = $stream[$j];
			$stream[$j] = $x;
			$result .= $data[$k] ^ chr($stream[($stream[$i] + $stream[$j]) % 256]);
		}
		return $result;
	}
	
	public function aes_encrypt($data, $key){
		return 'Not implemented yet';
	}
	
	public function blowfish_encrypt($data, $key){
		return 'Not implemented yet';
	}
	
	public function tripledes_encrypt($data, $key){
		return 'Not implemented yet';
	}
	
}

?>