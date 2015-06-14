<?php 

/** 
 *	@class Mysql
 *	@desc Concrete MySQL implementation for Data services interface
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
 *	@acknowledgements J R Harshath (Codefest 2010 website code)
 *
**/
class Mysql {
	/** 
	 *	@var $conn Connection resource
	**/
	protected $conn;
	
	/** 
	 *	@constructor 
	**/
	public function __construct($database, $user, $pass, $host){
		$this->open($database, $user, $pass, $host);
	}

	/** 
	 *	@interface DataService
	**/
	public function open($database, $user, $pass, $host){
		$this->conn = @mysql_connect($host, $user, $pass);
		if( $this->conn==false ) {
			$err = mysql_errno();
			if( $err==1203 ) {
				die('Our database server is overcome with connections. Please try after some time.');
			}
			die('Could not connect to database host');
		}
		@mysql_select_db($database, $this->conn)
			or die('Could not select database');
	}
	
	/** 
	 *	@interface DataService
	**/
	public function getResult($query, $type=0, $resulttype=MYSQL_BOTH){
		$resultset = @mysql_query($query, $this->conn);
		if($resultset === false) 
			return false;
		switch($type){
			case 0 : // Select
				$result = array();
				while( $rowset = mysql_fetch_array($resultset, $resulttype) ) {
					array_push( $result, $rowset );
				}
				return $result;
			case 1 : // Update Delete
				return mysql_affected_rows($this->conn);
			case 2 : // Insert
				return mysql_insert_id($this->conn);
		}
	}
	
	/** 
	 *	@interface DataService
	**/
	public function escape($param, $addslashes=false){
		if( $addslashes==false ) {
			if( get_magic_quotes_gpc() ) $param = stripslashes($param);
		} else {
			if( !get_magic_quotes_gpc() ) $param = addslashes($param);
		}
		$param = mysql_real_escape_string($param, $this->conn);
		
		return $param;
	}
	
	/** 
	 *	@interface DataService
	**/
	public function getAutoId(){
		return mysql_insert_id($this->conn);
	}
	
	/** 
	 *	@interface DataService
	**/
	public function close(){
		return mysql_close($this->conn);
	}
	
	/** 
	 *	@interface DataService
	**/
	public function getError(){
		return mysql_error($this->conn);
	}
	
	//public function getStatement();
}

?>