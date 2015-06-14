<?php 
require_once(SBSERVICE);

/**
 *	@class WebInfoWorkflow
 *	@desc Returns information about web entry between child and parent
 *
 *	@param parent long int Chain ID [memory]
 *	@param child long int Chain ID [memory]
 *	@param type string Type name [memory] optional default 'general'
 *
 *	@return web array Web information [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class WebInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('parent', 'child'),
			'optional' => array('type' => 'general')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Web information given successfully';
		
		$service = array(
			'service' => 'transpera.relation.select.workflow',
			'args' => array('parent', 'child', 'type'),
			'conn' => 'cbconn',
			'relation' => '`webs`',
			'sqlcnd' => "where `type`='\${type}' and `parent`=\${parent} and `child`=\${child}",
			'escparam' => array('type'),
			'check' => false,
			'output' => array('result' => 'web')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('web');
	}
	
}

?>