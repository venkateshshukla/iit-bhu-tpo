<?php 
require_once(SBSERVICE);

/**
 *	@class PersonListWorkflow
 *	@desc Returns all persons information in people container
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param string user User name [memory]
 *	@param peopleid long int People ID [memory] optional default PEOPLE_ID
 *	@param plname string People name [memory] optional default ''
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@return persons array Persons information [memory]
 *	@return peopleid long int People ID [memory]
 *	@return plname string People name [memory]
 *	@return admin integer Is admin [memory]
 *	@return total long int Paging Total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PersonListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user'),
			'optional' => array('peopleid' => PEOPLE_ID, 'plname' => '', 'pgsz' => false, 'pgno' => 0, 'total' => false),
			'set' => array('id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'peopleid', 'pname' => 'plname'),
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'sqlprj' => '`pnid`, `username`, `name`, `thumbnail`, `title`',
			'sqlcnd' => "where `pnid` in \${list} order by `name`",
			'type' => 'person',
			'successmsg' => 'Persons information given successfully',
			'output' => array('entities' => 'persons'),
			'mapkey' => 'pnid',
			'mapname' => 'person'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('persons', 'peopleid', 'plname', 'admin', 'total');
	}
	
}

?>