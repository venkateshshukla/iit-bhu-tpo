<?php 
require_once(SBSERVICE);

/**
 *	@class WebStateWorkflow
 *	@desc Edits state value in web member
 *
 *	@param child long int Chain ID [memory]
 *	@param parent long int Chain ID [memory]
 *	@param type string Type name [memory] optional default 'general'
 *	@param state string State value [memory] optional default '0'
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class WebStateWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('child', 'parent'),
			'optional' => array('state' => '0', 'type' => 'general')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Web state edited successfully';
		
		$service = array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('child', 'parent', 'state', 'type'),
			'conn' => 'cbconn',
			'relation' => '`webs`',
			'sqlcnd' => "set `state`='\${state}' where `type`='\${type}' and `parent`=\${parent} and child=\${child}",
			'escparam' => array('state', 'type')
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