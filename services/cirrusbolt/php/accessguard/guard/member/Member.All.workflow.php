<?php 
require_once(SBSERVICE);

/**
 *	@class MemberAllWorkflow
 *	@desc Returns member key IDs in chain
 *
 *	@param chainid long int Chain ID [memory]
 *	@param state string State [memory] optional default false (true= Not '0')
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@return result array Member key information [memory]
 *	@return total long int Paging total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class MemberAllWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('chainid'),
			'optional' => array('state' => false, 'pgsz' => false, 'pgno' => 0, 'total' => false, 'mapname' => 'member')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Member keys returned successfully';
		
		$last = '';
		$args = array('chainid');
		$escparam = array();
		
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
			'relation' => '`members`',
			'sqlprj' => '`chainkeyid` as `ckid`, `chainid`, `user`, `type`, `control`, `state`, UNIX_TIMESTAMP(`mtime`)*1000 as `mtime`',
			'sqlcnd' => "where `chainid`=\${chainid} $last",
			'escparam' => $escparam,
			'check' => false,
			'errormsg' => 'No Members',
			'mapkey' => 'ckid',
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