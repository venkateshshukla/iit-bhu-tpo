<?php 
require_once(SBSERVICE);

/**
 *	@class SecureReadWorkflow
 *	@desc Unsecures secure message to be used further
 *
 *	@param data object Data to be unsecured [memory]
 *	@param type string Decode type [memory] ('json', 'wddx', 'xml', 'plain', 'html')
 *	@param crypt string Crypt type [memory] optional default 'none' ('none', 'rc4', 'aes', 'blowfish', 'tripledes')
 *	@param key string Key used for decryption [memory] optional default false (generated from challenge)
 *	@param keyid string Key ID returned previously [memory] optional default false
 *	@param hash string Hash type [memory] optional default 'none' ('none', 'md5', 'sha1', 'crc32')
 *	@param user string Username if user not set [memory] optional default false
 *	@param context string Application context for email [memory] optional default false
 *
 *	@return result object Unsecured message [memory]
 *	@return key long int Key used for decryption [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SecureReadWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('data'),
			'optional' => array('crypt' => 'none', 'hash' => 'none', 'key' => false, 'keyid' => false, 'user' => false, 'context' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$user = $memory['user'];
		$flag = $user ? true : ($memory['crypt']!='none');
		$memory['result'] = '';
		
		$workflow = array(
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('data'),
			'opt' => true,
			'params' => array('data.hash' => 'value', 'data.user' => 'user', 'data.challenge' => 'challenge', 'data.message' => 'data')
		));
		
		if($memory['hash'] != 'none'){
			array_push($workflow, 
			array(
				'service' => 'cypher.data.hash.service',
				'input' => array('type' => 'hash')
			),
			array(
				'service' => 'cbcore.data.equal.service',
				'input' => array('data' => 'result'),
				'errormsg' => 'Message integrity check failed'
			));
		}
		
		if($flag){
			array_push($workflow, 
			array(
				'service' => 'guard.key.identify.workflow',
				'silent' => true
			));
		}
		
		if($memory['crypt'] != 'none'){
			array_push($workflow, 
			array(
				'service' => 'guard.key.identify.workflow'
			),
			array(
				'service' => 'cypher.data.decrypt.service',
				'input' => array('type' => 'crypt')
			),
			array(
				'service' => 'cbcore.data.decode.service',
				'input' => array('data' => 'result')
			));
		}
		
		$memory = Snowblozm::execute($workflow, $memory);
		
		if($memory['valid']){
			$memory['result'] = array_merge(is_array($memory['result']) ? $memory['result'] : array(), $memory['data']);
			if(isset($memory['result']['challenge'])) 
				unset($memory['result']['challenge']);
			if(isset($memory['result']['message'])) 
				unset($memory['result']['message']);
			if(isset($memory['result']['hash'])) 
				unset($memory['result']['hash']);
			if(isset($memory['result']['keyid'])) 
				unset($memory['result']['keyid']);
			
			if($flag)
				$memory['result']['keyid'] = $memory['keyid'];
			else 
				$memory['result']['keyid'] = -1;
			
			$memory['result']['user'] = $user ? $user : $memory['user'];
		}
		
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('result', 'key');
	}
	
}

?>