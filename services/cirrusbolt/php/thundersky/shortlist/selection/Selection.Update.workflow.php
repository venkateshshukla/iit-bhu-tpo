<?php 
require_once(SBSERVICE);

/**
 *	@class SelectionUpdateWorkflow
 *	@desc Edits all selections status information of stage
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param stageid long int Stage ID [memory]
 *	@param status integer Status  [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SelectionUpdateWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'stageid'),
			'optional' => array('status' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.edit.workflow',
			'input' => array('id' => 'stageid')
			'args' => array('status'),
			'conn' => 'cbslconn',
			'relation' => '`selections`',
			'sqlcnd' => "set `status`=\${status} where stageid=\${id}",
			'errormsg' => 'Invalid Stage ID',
			'successmsg' => 'Selections status updated successfully'
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