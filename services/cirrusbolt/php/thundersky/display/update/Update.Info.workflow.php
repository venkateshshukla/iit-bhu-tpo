<?php 
require_once(SBSERVICE);

/**
 *	@class UpdateInfoWorkflow
 *	@desc Returns update information by ID
 *
 *	@param updtid/id long int Update ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param user string Key User [memory]
 *	@param boardid long int Board ID [memory] optional default 0
 *	@param bname/name string Board name [memory] optional default ''
 *
 *	@return update array Update information [memory]
 *	@return bname string Board name [memory]
 *	@return boardid long int Board ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class UpdateInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('updtid'),
			'optional' => array('keyid' => false, 'user' => '', 'bname' => false, 'name' => '', 'boardid' => false, 'id' => 0),
			'set' => array('id', 'name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['updtid'] = $memory['updtid'] ? $memory['updtid'] : $memory['id'];
		
		$service = array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'updtid', 'parent' => 'boardid', 'cname' => 'name', 'pname' => 'bname'),
			'conn' => 'cbdconn',
			'relation' => '`updates`',
			'sqlcnd' => "where `updtid`=\${id}",
			'errormsg' => 'Invalid Update ID',
			'type' => 'update',
			'successmsg' => 'Update information given successfully',
			'output' => array('entity' => 'update')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('update', 'bname', 'boardid', 'admin');
	}
	
}

?>