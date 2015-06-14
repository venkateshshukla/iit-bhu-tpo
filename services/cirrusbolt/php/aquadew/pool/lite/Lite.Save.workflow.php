<?php 
require_once(SBSERVICE);

/**
 *	@class LiteSaveWorkflow
 *	@desc Saves Data to Cache using CacheLite after encoding
 *
 *	@param key string Key [memory]
 *	@param data string Data [memory] 
 *	@param expiry long int Expiry time [memory] optional default false
 *	@param type string Request type [memory] optional default 'json' ('json', 'xml', 'wddx')
 *	@param cachelite array CacheLite configuration [Snowblozm] (caching, cacheDir, lifeTime, automaticCleaningFactor, hashedDirectoryLevel, automaticSerialization)
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class LiteSaveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('key', 'data'),
			'optional' => array('expiry' => false, 'type' => 'json')
		);
	}

	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Data Cached Successfully';
		
		$workflow = array(
		array(
			'service' => 'cbcore.data.encode.service'
		),
		array(
			'service' => 'pool.lite.save.service',
			'input' => array('data' => 'result')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>