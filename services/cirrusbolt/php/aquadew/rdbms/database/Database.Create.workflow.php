<?php 
require_once(SBSERVICE);

/**
 *	@class DatabaseCreateWorkflow
 *	@desc Creates new database and user
 *
 *	@param dbname string Database name [memory]
 *	@param dbpass string Database password [memory]
 *
 *	@param conn array DataService instance configuration key [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class DatabaseCreateWorkflow implements Service {
	
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
		$memory['msg'] = 'Database created successfully';
		
		$workflow = array(
		array(
			'service' => 'rdbms.query.execute.workflow',
			'args' => array('dbname', 'dbpass'),
			'query' => "create user '\${dbname}'@'localhost' identified by '\${dbpass}';",
			'rstype' => 1,
			'count' => 0,
			'escparam' => array('dbname', 'dbpass'),
			'errormsg' => 'Invalid Database Name / Password'
		),
		array(
			'service' => 'rdbms.query.execute.workflow',
			'args' => array('dbname'),
			'query' => "create database \${dbname};",
			'rstype' => 1,
			'count' => 1,
			'escparam' => array('dbname'),
			'errormsg' => 'Invalid Database Name'
		),
		array(
			'service' => 'rdbms.query.execute.workflow',
			'args' => array('dbname'),
			'query' => "grant all privileges on \${dbname}.* to '\${dbname}'@'localhost';",
			'rstype' => 1,
			'count' => 0,
			'escparam' => array('dbname'),
			'errormsg' => 'Invalid Database Name'
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