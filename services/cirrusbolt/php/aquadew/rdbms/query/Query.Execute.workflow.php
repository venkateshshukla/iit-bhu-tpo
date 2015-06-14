<?php 
require_once(SBSERVICE);

/**
 *	@class QueryExecuteWorkflow
 *	@desc Executes query by performing escapes, checks and substitutions and validates result
 *
 *	@param conn array DataService instance configuration key [memory] (type, user, pass, host, database)
 *	@param args array Query parameters [args]
 *	@param query string SQL Query to be executed with substitutions [memory]
 *	@param rstype integer type of result [memory] optional default 0 
 *	@param escparam array Escape parameters [memory] optional default array() 
 *	@param numparam array Number parameters [memory] optional default args-escparam
 *	@param check boolean Is validate [memory] optional default true
 *	@param count integer Validation count [memory] optional default 1
 *	@param not boolean Error on nonequality [memory] optional default true
 *	@param errormsg string Error message on validation failure [memory] optional default 'Invalid Query Results'
 *	@param errstatus integer Error status code [memory] optional default 505
 *
 *	@return sqlresult array/integer Result set / affected row count [memory]
 *	@return sqlrc integer Row count [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class QueryExecuteWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('conn', 'query'),
			'optional' => array('rstype' => 0, 'escparam' => array(), 'numparam' => false, 
								'count' => 1, 'not' => true, 'errormsg' => 'Invalid Query Results', 'errstatus' => 505, 'check' => true)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$args = $memory['args'];
		$escparam = $memory['escparam'];
		$numparam = $memory['numparam'] ? $memory['numparam'] : array_diff($args, $escparam);
		$key = $memory['conn'];
		
		$conn = Snowblozm::get($key);
		switch($conn['type']){
			case 'mysql' :
			default :
				require_once(CBMYSQL);
				$dataservice = new Mysql($conn['database'], $conn['user'], $conn['pass'], $conn['host'], isset($conn['persist']) ? $conn['persist'] : false);
				break;
		}
		$memory['conn'] = $dataservice;
		
		$workflow = array();
		
		if(count($numparam) != 0){
			array_push($workflow, array(
				'service' => 'cbcore.data.numeric.service',
				'args' => $numparam,
				'errormsg' => $memory['errormsg'].' - Value Not Numeric'
			));
		}
		
		if(count($escparam) != 0){
			array_push($workflow, array(
				'service' => 'rdbms.query.escape.service',
				'args' => $escparam
			));
		}
		
		if(count($args) != 0){
			array_push($workflow, array(
				'service' => 'cbcore.data.substitute.service',
				'args' => $args,
				'input' => array('data' => 'query'),
				'output' => array('result' => 'query')
			));
		}
		
		array_push($workflow, array(
			'service' => 'rdbms.query.execute.service',
			'output' => array('sqlresult' => 'sqlresult', 'sqlrowcount' => 'sqlrc')
		));
		
		if($memory['check']){
			array_push($workflow, array(
				'service' => 'cbcore.data.equal.service',
				'input' => array('data' => 'sqlrc'),
				'value' => $memory['count']
			));
		}
		
		$memory = Snowblozm::execute($workflow, $memory);
		
		$dataservice->close();
		$memory['conn'] = $key;
		
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('sqlresult', 'sqlrc');
	}
	
}

?>