<?php 

/**
 *	@interface DataService
 *	@desc Abstract interface for data services 
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
interface DataService {

	/** 
	 *	@method open
	 *	@desc open connection to data service
	 *
	 *	@param $database string Database name
	 *	@param $user string Database user
	 *	@param $pass string Database password
	 *	@param $host string Database host
	 *
	**/
	public function open($database, $user, $pass, $host);
	
	/** 
	 *	@method getResult
	 *	@desc executes query and returns resultset if execute is false, else return affected row count
	 *
	 *	@param $query string SQL query
	 *	@param $type integer Execute type (0=Select 1=Update/Delete 2=Insert)
	 *	@param $resulttype MySQL constant
	 *
	 *	@return $result array/integer/false
	 *
	**/
	public function getResult($query, $execute=false, $resulttype=MYSQL_NUM);
	
	/** 
	 *	@method escape
	 *	@desc escapes parameter strings array
	 *
	 *	@param $param string
	 *
	 *	@return $result string
	 *
	**/
	public function escape($param);
	
	/** 
	 *	@method getAutoId
	 *	@desc gets the last auto-increment id
	 *
	 *	@return $result long int
	 *
	**/
	public function getAutoId();
	
	/** 
	 *	@method close
	 *	@desc closes the connection
	 *
	**/
	public function close();
	
	/** 
	 *	@method getError
	 *	@desc gets the last error
	 *
	 *	@return $result string Error information
	 *
	**/
	public function getError();
	
	//public function getStatement();
}

?>