<?php 
require_once(SBSERVICE);

/**
 *	@class WebChildrenWorkflow
 *	@desc Lists child chains of parent in the web
 *
 *	@param parent long int Chain ID [memory]
 *	@param type string Type name [memory] optional default 'general'
 *	@param istate string State [memory] optional default false (true= Not '0')
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@return children array Children IDs [memory]
 *	@return total long int Paging total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class WebChildrenWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('parent'),
			'optional' => array('type' => 'general', 'istate' => false, 'pgsz' => false, 'pgno' => 0, 'total' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Web children listed successfully';
		
		$last = '';
		$args = array('parent', 'type');
		$escparam = array('type');
		
		if($memory['istate'] === true){
			$last = " and `state`<>'0' ";
		}
		else if($memory['istate']){
			$last = " and `state`='\${istate}' ";
			array_push($escparam, 'istate');
			array_push($args, 'istate');
		}	
		
		$service = array(
			'service' => 'transpera.relation.select.workflow',
			'args' => $args,
			'conn' => 'cbconn',
			'relation' => '`webs`',
			'sqlprj' => '`child`',
			'sqlcnd' => "where `parent`=\${parent} and `type`='\${type} $last'",
			'escparam' => $escparam,
			'check' => false,
			'output' => array('result' => 'children'),
			'mapkey' => 'child',
			'mapname' => 'web'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('children', 'total');
	}
	
}

?>