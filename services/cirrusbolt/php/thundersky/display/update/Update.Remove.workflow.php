<?php 
require_once(SBSERVICE);

/**
 *	@class UpdateRemoveWorkflow
 *	@desc Removes update by ID
 *
 *	@param updtid long int Update ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param boardid long int Board ID [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class UpdateRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'updtid'),
			'optional' => array('boardid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.remove.workflow',
			'input' => array('id' => 'updtid', 'parent' => 'boardid'),
			'conn' => 'cbdconn',
			'relation' => '`updates`',
			'type' => 'update',
			'sqlcnd' => "where `updtid`=\${id}",
			'errormsg' => 'Invalid Update ID',
			'successmsg' => 'Update removed successfully'
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