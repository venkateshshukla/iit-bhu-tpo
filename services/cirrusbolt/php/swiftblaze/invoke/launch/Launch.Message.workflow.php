<?php 
require_once(SBSERVICE);

/**
 *	@class LaunchMessageWorkflow
 *	@desc Launches workflows from messages
 *
 *	@param source string Source type [memory] optional default 'stream' ('stream', 'path_info', 'query_string')
 *	@param reqtype string request type [memory] optional default 'post' ('get', 'post', 'json', 'wddx', 'xml')
 *	@param restype string response types [memory] optional default 'json' ('json', 'wddx', 'xml', 'plain', 'html'),
 *	@param crypt string Crypt types [memory] optional default 'none' ('none', 'rc4', 'aes', 'blowfish', 'tripledes')
 *	@param hash string Hash types [memory] optional default 'none' ('none', 'md5', 'sha1', 'crc32')
 *	@param access array allowed service provider names [memory] optional default false
 *	@param pages array allowed pages [memory] optional default array()
 *	@param user string Username to be used if not set in message [memory] optional default false
 *	@param context string Application context for email [memory] optional default false
 * 	@param raw boolean Raw output required [memory] optional default false
 *
 *	@result result string Result [memory]
 *	@result request object Request [memory]
 *	@result response object Response [memory]
 *	@result uri Service URI [memory]
 *	@return data string HTTP Data [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class LaunchMessageWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array(
				'source' => 'stream',
				'reqtype' => 'post', 
				'restype' => 'json', 
				'crypt' => 'none', 
				'hash' => 'none', 
				'access' => array(), 
				'pages' => array(), 
				'user' => false, 
				'context' => false, 
				'raw' => false,
				'emergency' => false
			)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$workflow = array(
		array(
			'service' => 'invoke.http.read.service'
		),
		array(
			'service' => 'invoke.transport.read.workflow',
			'input' => array('type' => 'reqtype')
		),
		array(
			'service' => 'invoke.launch.check.service',
			'args' => array('valid', 'msg', 'status', 'details', 'pages', 'emergency'),
			'params' => array('pages', 'emergency'),
			'input' => array('message' => 'result'),
			'strict' => false
		),
		array(
			'service' => 'invoke.launch.message.service',
			'strict' => false
		),
		array(
			'service' => 'invoke.transport.write.workflow',
			'input' => array('data' => 'message', 'type' => 'restype'),
			'strict' => false
		));
		
		if(!$memory['raw']){
			array_push($workflow, array(
				'service' => 'invoke.http.write.service',
				'input' => array('data' => 'result', 'type' => 'restype')
			));
		}
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('result', 'request', 'response', 'uri', 'data');
	}
	
}

?>