<?php 
require_once(SBSERVICE);

/**
 *	@class MemberInfoWorkflow
 *	@desc Returns member information in chain
 *
 *	@param chainid long int Chain ID [memory]
 *	@param user string Key user [memory]
 *	@param state string State [memory] optional default false (true= Not '0')
 *
 *	@return result array Member key information [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class MemberInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('chainid', 'user'),
			'optional' => array('state' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Member information returned successfully';
		
		$last = '';
		$args = array('chainid', 'user');
		$escparam = array('user');
		
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
			'relation' => '`members`',
			'sqlprj' => '`chainkeyid` as `ckid`, `chainid`, `user`, `type`, `control`, `state`, UNIX_TIMESTAMP(`mtime`)*1000 as `mtime`',
			'sqlcnd' => "where `chainid`=\${chainid} and `keyid`=(select `keyid` from `keys` where `user`='\${user}') $last",
			'escparam' => $escparam,
			'errormsg' => 'No Members'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0' => 'result')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('result');
	}
	
}

?>