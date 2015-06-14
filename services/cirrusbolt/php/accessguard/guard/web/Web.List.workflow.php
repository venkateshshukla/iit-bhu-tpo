<?php 
require_once(SBSERVICE);

/**
 *	@class WebListWorkflow
 *	@desc Returns chain IDs in web for member key ID
 *
 *	@param keyid long int Key ID [memory]
 *	@param parent long int Parent Chain ID [memory]
 *	@param state string State [memory] optional default false (true= Not '0')
 *	@param istate string State Inherit [memory] optional default false (true= Not '0')
 *	@param type string Type name [memory] optional default 'general'
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@return result array Chain IDs [memory]
 *	@return total long int Paging total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class WebListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'parent'),
			'optional' => array('type' => 'general', 'state' => false, 'istate' => false, 'pgsz' => false, 'pgno' => 0, 'total' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Web chains returned successfully';
		$last = $ilast = '';
		$args = array('keyid', 'parent', 'type', 'state');
		$escparam = array('type');
		
		if($memory['state'] === true){
			$last = " and `state`<>'0' ";
		}
		else if($memory['state']){
			$last = " and `state`='\${state}' ";
			array_push($args, 'state');
			array_push($escparam, 'state');
		}
		
		if($memory['istate'] === true){
			$ilast = " and `state`<>'0' ";
		}
		else if($memory['istate']){
			$ilast = " and `state`='\${istate}' ";
			array_push($args, 'istate');
			array_push($escparam, 'istate');
		}
		
		$service = array(
			'service' => 'transpera.relation.select.workflow',
			'args' => $args,
			'conn' => 'cbconn',
			'relation' => '`webs`',
			'sqlprj' => "(case `inherit` when 1 then `child` when 0 then (select `chainid` from `members` where `chainid`=`child` and `keyid`=\${keyid} $last) end) as `child`",
			'sqlcnd' => "where `type`='\${type}' and `parent`=\${parent} $ilast",
			'escparam' => $escparam,
			'check' => false,
			'errormsg' => 'No Access',
			'mapkey' => 'child',
			'mapname' => 'web'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('result', 'total');
	}
	
}

?>