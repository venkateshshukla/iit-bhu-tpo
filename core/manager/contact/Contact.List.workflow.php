<?php 
require_once(SBSERVICE);

/**
 *	@class ContactListWorkflow
 *	@desc Returns all contacts information in manager
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param mngrid/id long int Manager ID [memory] optional default MANAGER_ID
 *	@param mngrname/name string Manager name [memory] optional default ''
 *
 *	@param pgsz long int Paging Size [memory] optional default 25
 *	@param pgno long int Paging Index [memory] optional default 0
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return contacts array Contacts information [memory]
 *	@return mngrid long int Manager ID [memory]
 *	@return mngrname string Manager Name [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return pgsz long int Paging Size [memory]
 *	@return pgno long int Paging Index [memory]
 *	@return total long int Paging Total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ContactListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'mngrid' => false, 'id' => MANAGER_ID, 'mngrname' => false, 'name' => '', 'pgsz' => false, 'pgno' => 0, 'total' => false, 'padmin' => true),
			'set' => array('pgno', 'id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['mngrid'] = $memory['mngrid'] ? $memory['mngrid'] : $memory['id'];
		$memory['mngrname'] = $memory['mngrname'] ? $memory['mngrname'] : $memory['name'];
		
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'mngrid', 'pname' => 'mngrname'),
			'conn' => 'exconn',
			'relation' => '`contacts`',
			'type' => 'contact',
			'sqlcnd' => "where `cntid` in \${list} order by `priority` desc",
			'successmsg' => 'Contacts information given successfully',
			'lsttrack' => false,
			'output' => array('entities' => 'contacts'),
			'mapkey' => 'cntid',
			'mapname' => 'contact',
			'saction' => 'add',
			'iaction' => 'add',
		);
		//Snowblozm::$debug = true;
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('contacts', 'mngrid', 'mngrname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz');
	}
	
}

?>