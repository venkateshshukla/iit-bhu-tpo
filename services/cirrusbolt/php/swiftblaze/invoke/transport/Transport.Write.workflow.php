<?php 
require_once(SBSERVICE);

/**
 *	@class TransportWriteWorkflow
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
 *	@param ui array UI data [memory] optional default false
 *
 *	@return result string Secured message [memory]
 *	@return response object Secured message [memory]
 *	@return key long int Key used for encryption [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class TransportWriteWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('type', 'crypt', 'hash'),
			'optional' => array('data' => array(), 'ui' => false, 'key' => false, 'keyid' => false, 'challenge' => false, 'user' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		if($memory['keyid'] === false)
			$args = array('user', 'message', 'hash');
		else
			$args = array('user', 'challenge', 'message', 'hash');
		
		$workflow = array(
		array(
			'service' => 'cypher.secure.write.workflow',
			'output' => array('result' => 'message')
		),
		array(
			'service' => 'cbcore.data.prepare.service',
			'args' => $args,
			'strict' => false,
			'output' => array('result' => 'response')
		),
		array(
			'service' => 'cbcore.data.encode.service',
			'input' => array('data' => 'response')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('result', 'response', 'key');
	}
	
}

?>