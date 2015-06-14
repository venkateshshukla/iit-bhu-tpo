<?php 
require_once(SBSERVICE);

/**
 *	@class DatabasePasswordWorkflow
 *	@desc Sets password for database and user by name
 *
 *	@param dbname string Database name [memory]
 *	@param dbpass string Database password [memory]
 *
 *	@param conn array DataService instance configuration key [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class DatabasePasswordWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('conn', 'dbname', 'dbpass')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Database password set successfully';
		
		$service = array(
			'service' => 'rdbms.query.execute.workflow',
			'args' => array('dbname', 'dbpass'),
			'query' => "set password for '\${dbname}'@'localhost' = password('\${dbpass}');",
			'rstype' => 1,
			'count' => 0,
			'escparam' => array('dbname', 'dbpass'),
			'errormsg' => 'Invalid Database Name / Password'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>