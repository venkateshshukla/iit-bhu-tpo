<?php 
require_once(SBSERVICE);

/**
 *	@class WebRemoveWorkflow
 *	@desc Removes web member using child and parent chain IDs
 *
 *	@param child long int Chain ID [memory]
 *	@param parent long int Chain ID [memory]
 *	@param type string Type name [memory] optional default 'general'
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class WebRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('child', 'parent'),
			'optional' => array('type' => 'general')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Web member removed successfully';
		
		$service = array(
			'service' => 'transpera.relation.delete.workflow',
			'args' => array('child', 'parent', 'type'),
			'conn' => 'cbconn',
			'relation' => '`webs`',
			'sqlcnd' => "where `type`='\${type}' and `child`=\${child} and `parent`=\${parent}",
			'escparam' => array('type'),
			'errormsg' => 'Invalid Parent Chain ID'
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