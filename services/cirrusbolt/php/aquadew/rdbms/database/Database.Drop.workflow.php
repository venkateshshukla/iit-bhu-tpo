<?php 
require_once(SBSERVICE);

/**
 *	@class DatabaseDropWorkflow
 *	@desc Drops database and user by name
 *
 *	@param dbname string Database name [memory]
 *
 *	@param conn array DataService instance configuration key [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class DatabaseDropWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('conn', 'dbname')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Database dropped successfully';
		
		$workflow = array(
		array(
			'service' => 'rdbms.query.execute.workflow',
			'args' => array('dbname'),
			'query' => "revoke all privileges on \${dbname}.* from '\${dbname}'@'localhost';",
			'rstype' => 1,
			'count' => 0,
			'escparam' => array('dbname'),
			'errormsg' => 'Invalid Database Name'
		),
		array(
			'service' => 'rdbms.query.execute.workflow',
			'args' => array('dbname', 'dbpass'),
			'query' => "drop user '\${dbname}'@'localhost';",
			'rstype' => 1,
			'count' => 0,
			'escparam' => array('dbname', 'dbpass'),
			'errormsg' => 'Invalid Database Name'
		),
		array(
			'service' => 'rdbms.query.execute.workflow',
			'args' => array('dbname'),
			'query' => "drop database \${dbname};",
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