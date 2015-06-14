<?php 
require_once(SBSERVICE);

/**
 *	@class WebParentWorkflow
 *	@desc Returns unique parent chain of child in the web
 *
 *	@param child long int Chain ID [memory]
 *	@param type string Type name [memory] optional default 'general'
 *	@param state string State [memory] optional default false (true= Not '0')
 *
 *	@return web array Web member information [memory]
 *	@return parent long int Chain ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class WebParentWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('child'),
			'optional' => array('type' => 'general', 'state' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Web parent given successfully';
		
		$last = '';
		$args = array('child', 'type');
		$escparam = array('type');
		
		if($memory['state'] === true){
			$last = " and `state`<>'0' ";
		}
		else if($memory['state']){
			$last = " and `state`='\${state}' ";
			array_push($escparam, 'state');
			array_push($args, 'state');
		}	
		
		$workflow = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => $args,
			'conn' => 'cbconn',
			'relation' => '`webs`',
			'sqlcnd' => "where `type`='\${type}' and `child`=\${child} $last",
			'escparam' => $escparam,
			'errormsg' => 'Unable to find unique parent'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0' => 'web', 'result.0.parent' => 'parent')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('web', 'parent');
	}
	
}

?>