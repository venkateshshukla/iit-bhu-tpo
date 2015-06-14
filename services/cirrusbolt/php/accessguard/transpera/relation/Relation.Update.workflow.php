<?php 
require_once(SBSERVICE);

/**
 *	@class RelationUpdateWorkflow
 *	@desc Executes UPDATE query on relation
 *
 *	@param relation string Relation name [memory]
 *	@param sqlcnd string SQL condition [memory]
 *	@param args array Query parameters [args]
 *	@param escparam array Escape parameters [memory] optional default array()
 *	@param check boolean Is validate [memory] optional default true
 *	@param not boolean Value check nonequal [memory] optional default true
 *	@param count boolean Value [memory] optional default 1
 *	@param errormsg string Error message [memory] optional default 'Invalid Tuple / No Change'
 *
 *	@param conn array DataService instance configuration key [memory]
 *
 *	@return sqlrc integer Row count [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class RelationUpdateWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('conn', 'relation', 'sqlcnd'),
			'optional' => array('escparam' => array(), 'errormsg' => 'Invalid Tuple / No Change', 'not' => true, 'count' => 1, 'check' => true)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'rdbms.query.execute.workflow',
			'args' => $memory['args'],
			'query' => 'update '.$memory['relation'].' '.$memory['sqlcnd'].';',
			'rstype' => 1
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('sqlrc');
	}
	
}

?>