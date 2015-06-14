<?php 
require_once(SBSERVICE);

/**
 *	@class DirectoryListWorkflow
 *	@desc Returns all directories information in storage
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param stgid long int Storage ID [memory] optional default 0
 *	@param stgname string Storage name [memory] optional default ''
 *
 *	@return directories array Directory information [memory]
 *	@return stgid long int Storage ID [memory]
 *	@return stgname string Storage name [memory]
 *	@return admin integer Is admin [memory] 
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class DirectoryListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('stgid' => false, 'id' => 0, 'stgname' => false, 'name' => ''),
			'set' => array('id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['stgid'] = $memory['stgid'] ? $memory['stgid'] : $memory['id'];
		$memory['stgname'] = $memory['stgname'] ? $memory['stgname'] : $memory['name'];
		
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'stgid'),
			'conn' => 'cbsconn',
			'relation' => '`directories`',
			'type' => 'directory',
			'sqlprj' => '`dirid`, `owner`, `name`, `path`',
			'sqlcnd' => "where `dirid` in \${list} order by `name`",
			'output' => array('entities' => 'directories'),
			'check' => false,
			'successmsg' => 'Directories information given successfully',
			'mapkey' => 'dirid',
			'mapname' => 'directory',
			'saction' => 'add'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('directories', 'admin', 'stgid', 'stgname');
	}
	
}

?>