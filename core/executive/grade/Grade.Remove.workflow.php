<?php 
require_once(SBSERVICE);

/**
 *	@class GradeRemoveWorkflow
 *	@desc Removes grade by ID
 *
 *	@param gradeid long int Grade ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 
 *	@param batchid long int Batch ID [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class GradeRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'gradeid'),
			'optional' => array('batchid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.remove.workflow',
			'input' => array('id' => 'gradeid', 'parent' => 'batchid'),
			'conn' => 'exconn',
			'relation' => '`grades`',
			'type' => 'grade',
			'sqlcnd' => "where `gradeid`=\${id}",
			'errormsg' => 'Invalid Grade ID',
			'successmsg' => 'Grade removed successfully'
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