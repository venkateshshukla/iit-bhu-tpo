<?php 
require_once(SBSERVICE);

/**
 *	@class RelationDeleteWorkflow
 *	@desc Executes DELETE query on relation
 *
 *	@param relation string Relation name [memory]
 *	@param sqlcnd string SQL condition [memory]
 *	@param args array Query parameters [args]
 *	@param check boolean Is validate [memory] optional default true
 *	@param count integer Validation count [message] optional default 1
 *	@param not boolean Error on nonequality [message] optional default true
 *	@param escparam array Escape parameters [memory] optional default array()
 *	@param errormsg string Error message [memory] optional default 'Invalid Tuple'
 *
 *	@param conn array DataService instance configuration key [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class RelationDeleteWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('conn', 'relation', 'sqlcnd'),
			'optional' => array('escparam' => array(), 'errormsg' => 'Invalid Tuple', 'not' => true, 'count' => 1, 'check' => true)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'rdbms.query.execute.workflow',
			'args' => $memory['args'],
			'query' => 'delete from '.$memory['relation'].' '.$memory['sqlcnd'].';',
			'rstype' => 1
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