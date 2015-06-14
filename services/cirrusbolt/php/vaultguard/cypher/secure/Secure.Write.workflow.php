<?php 
require_once(SBSERVICE);

/**
 *	@class SecureWriteWorkflow
 *	@desc Builds secure message to be used further
 *
 *	@param data object Data to be secured [memory] optional default array()
 *	@param type string Encode type [memory] ('json', 'wddx', 'xml', 'plain', 'html')
 *	@param crypt string Crypt type [memory] ('none', 'rc4', 'aes', 'blowfish', 'tripledes')
 *	@param key string Key used for encryption [memory] optional default false (generated from challenge)
 *	@param challenge string Challenge to be used while hashing [memory] optional default false
 *	@param keyid string Key ID returned previously [memory] optional default false
 *	@param hash string Hash type [memory] ('none', 'md5', 'sha1', 'crc32')
 *	@param user string Username [memory] optional default false
 *
 *	@return result string Secured message [memory]
 *	@return key long int Key used for encryption [memory]
 *	@return hash string Hash of secured message [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SecureWriteWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('type', 'crypt', 'hash'),
			'optional' => array('data' => array(), 'key' => false, 'keyid' => false, 'challenge' => false, 'user' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$type = ($memory['crypt'] == 'none') ? 'none' : $memory['type'];
		
		$strict = isset($memory['data']['valid']) && $memory['data']['valid'];
		
		$workflow = array(
		array(
			'service' => 'guard.key.identify.workflow',
			'silent' => $memory['crypt'] == 'none'
		),
		array(
			'service' => 'cbcore.data.encode.service',
			'strict' => $strict,
			'type' => $type
		));
		
		if($memory['crypt'] != 'none'){
			array_push($workflow, 
			array(
				'service' => 'cypher.data.encrypt.service',
				'input' => array('data' => 'result', 'type' => 'crypt')
			));
		}
		
		if($memory['hash'] != 'none'){
			array_push($workflow, 
			array(
				'service' => 'cypher.data.hash.service',
				'input' => array('data' => 'result', 'type' => 'hash'),
				'output' => array('result' => 'hash')
			));
		}
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('result', 'key', 'hash');
	}
	
}

?>