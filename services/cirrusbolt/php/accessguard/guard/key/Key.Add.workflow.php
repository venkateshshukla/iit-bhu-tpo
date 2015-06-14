<?php 
require_once(SBSERVICE);

/**
 *	@class KeyAddWorkflow
 *	@desc Adds new service key
 *
 *	@param user string Username [memory]
 *	@param key string Key value [memory]
 *
 *	@return return id long int Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class KeyAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('key', 'user')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Key created successfully';
		
		$service = array(
			'service' => 'transpera.relation.insert.workflow',
			'args' => array('key', 'user'),
			'conn' => 'cbconn',
			'relation' => '`keys`',
			'sqlcnd' => "(`user`, `keyvalue`) values ('\${user}', MD5('\${user}\${key}'))",
			'escparam' => array('key', 'user')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('id');
	}
	
}

?>