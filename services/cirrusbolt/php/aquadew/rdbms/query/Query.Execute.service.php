<?php 
require_once(SBSERVICE);
require_once(CBMYSQL);

/**
 *	@class QueryExecuteService
 *	@desc Executes a query and returns result according to rstype
 *
 *	@param query string SQL Query [memory]
 *	@param rstype integer type of result [memory] optional default 0
 *	@param rsboth boolean type of resultset [memory] optional default MYSQL_ASSOC
 *	@param conn resource DataService instance [memory]
 *
 *	@return sqlresult array SQL Query ResultSet [memory]
 *	@return sqlrowcount integer resultset or affected row count [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class QueryExecuteService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('query', 'conn'),
			'optional' => array('rstype' => 0, 'rsboth' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$conn = $memory['conn'];
		$query = $memory['query'];
		$rstype = $memory['rstype'];
		$rsboth = $memory['rsboth'] ? MYSQL_BOTH : MYSQL_ASSOC;
		//echo $query;
		$result = $conn->getResult($query, $rstype, $rsboth);
		
		if($result === false){
			$memory['valid'] = false;
			$memory['msg'] = 'Error in Database';
			$memory['status'] = 585;
			$memory['details'] = 'Error : '.$conn->getError().' @query.execute.service';
			return $memory;
		}
		
		switch($rstype){
			case 0 :
				$memory['sqlresult'] = $result;
				$memory['sqlrowcount'] = count($result);
				break;
			case 1 :
				$memory['sqlresult'] = $result;
				$memory['sqlrowcount'] = $result;
				break;
			case 2 :
				$memory['sqlresult'] = $result;
				$memory['sqlrowcount'] = 1;
				break;
			default :
				$memory['valid'] = false;
				$memory['msg'] = 'Invalid Result Type';
				$memory['status'] = 503;
				$memory['details'] = 'Result type : '.$rstype.' not supported @query.execute.service';
				return $memory;
		}
		
		$memory['valid'] = true;
		$memory['msg'] = 'Valid Query Execution';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('sqlresult', 'sqlrowcount');
	}
	
}

?>