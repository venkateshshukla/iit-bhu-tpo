<?php 
require_once(SBSERVICE);
require_once(CACHELITEOUTPUT);

/**
 *	@class LiteOutputService
 *	@desc Saves Output Data to Cache using CacheLiteOutput
 *
 *	@param key string Key [memory] optional default false
 *	@param cachelite array CacheLite configuration [Snowblozm] (caching, cacheDir, lifeTime, automaticCleaningFactor, hashedDirectoryLevel, automaticSerialization)
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class LiteOutputService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('key' => false)
		);
	}

	/**
	 *	@interface Service
	**/
	public function run($memory){
		$options = Snowblozm::get('cachelite');
		$cache = new Cache_Lite_Output($options);
		
		if(!$cache){
			$memory['valid'] = false;
			$memory['msg'] = 'Error Initializing Cache';
			$memory['status'] = 200;
			$memory['details'] = 'Error initializing cache @pool.lite.output service';
			return $memory;
		}
		
		if($memory['key']){
			if($cache->start($memory['key']) === false){
				$memory['valid'] = false;
				$memory['msg'] = 'Error Output Data';
				$memory['status'] = 200;
				$memory['details'] = 'Error output data from cache @pool.lite.output service';
				return $memory;
			}
		}
		else {
			$cache->end();
		}
		
		$memory['data'] = $data;
		$memory['valid'] = true;
		$memory['msg'] = 'Data Output Successfully';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('data');
	}
	
}

?>