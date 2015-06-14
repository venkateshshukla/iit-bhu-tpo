<?php 
require_once(SBSERVICE);

/**
 *	@class WebParentsWorkflow
 *	@desc Lists parent chains of child in the web
 *
 *	@param child long int Chain ID [memory]
 *	@param type string Type name [memory] optional default 'general'
 *	@param state string State [memory] optional default false (true= Not '0')
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@return parents array Parents IDs [memory]
 *	@return total long int Paging total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class WebParentsWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('child'),
			'optional' => array('type' => 'general', 'state' => false, 'pgsz' => false, 'pgno' => 0, 'total' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Web parents listed successfully';
		
		$last = '';
		$args = array('child', 'type');
		$escparam =array('type');
		
		if($memory['state'] === true){
			$last = " and `state`<>'0' ";
		}
		else if($memory['state']){
			$last = " and `state`='\${state}' ";
			array_push($escparam, 'state');
			array_push($args, 'state');
		}	
		
		$service = array(
			'service' => 'transpera.relation.select.workflow',
			'args' => $args,
			'conn' => 'cbconn',
			'relation' => '`webs`',
			'sqlcnd' => "where `type`='\${type}' and `child`=\${child} $last",
			'escparam' => $escparam,
			'check' => false,
			'output' => array('result' => 'parents'),
			'mapkey' => 'parent',
			'mapname' => 'web'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('parents', 'type');
	}
	
}

?>