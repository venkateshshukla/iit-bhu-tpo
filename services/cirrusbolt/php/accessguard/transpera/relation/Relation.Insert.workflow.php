<?php 
require_once(SBSERVICE);

/**
 *	@class RelationInsertWorkflow
 *	@desc Executes INSERT query on relation
 *
 *	@param relation string Relation name [memory]
 *	@param sqlcnd string SQL condition [memory]
 *	@param args array Query parameters [args]
 *	@param escparam array Escape parameters [memory] optional default array()
 *	@param errormsg string Error message [memory] optional default 'Error in Database'
 *
 *	@param conn array DataService instance configuration key [memory]
 *
 *	@return return id long int Tuple ID [memory] 
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class RelationInsertWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('conn', 'relation', 'sqlcnd'),
			'optional' => array('escparam' => array(), 'errormsg' => 'Error in Database')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'rdbms.query.execute.workflow',
			'args' => $memory['args'],
			'output' => array('sqlresult' => 'id'),
			'query' => 'insert into '.$memory['relation'].' '.$memory['sqlcnd'].';',
			'rstype' => 2
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