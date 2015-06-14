<?php 
require_once(SBSERVICE);

/**
 *	@class BoardRemoveWorkflow
 *	@desc Removes board by ID
 *
 *	@param boardid long int Board ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param forumid long int Forum ID [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class BoardRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'boardid'),
			'optional' => array('forumid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.remove.workflow',
			'input' => array('id' => 'boardid', 'parent' => 'forumid'),
			'conn' => 'cbdconn',
			'relation' => '`boards`',
			'type' => 'board',
			'sqlcnd' => "where `boardid`=\${id}",
			'errormsg' => 'Invalid Board ID',
			'successmsg' => 'Board removed successfully'
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