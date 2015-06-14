<?php 
require_once(SBSERVICE);

/**
 *	@class ChainAuthorizeWorkflow
 *	@desc Authorizes key for chain operations and returns admin flag for action
 *
 *	@param chainid long int Chain ID [memory]
 *	@param keyid long int Key ID [memory]
 *
 *	@param cstate string State [memory] optional default false (true= Not '0')
 *	@param action string Action to authorize member [memory] optional default 'edit'
 *	@param mstate string State to authorize member [memory] optional default false (true= Not '0')
 *	@param iaction string Action to authorize inherit [memory] optional default 'edit'
 *	@param istate string State to authorize inherit [memory] optional default false (true= Not '0')
 *	@param init boolean init flag [memory] optional default true
 *	@param self boolean self flag [memory] optional default false
 *	@param admin boolean Is return admin flag [memory] optional default false
 *	@param inherit integer Check inherit [memory] optional default 1
 *	@param errormsg string Error msg [memory] optional default 'Unable to Authorize'
 *	@param custom array Custom Check Workflow [memory] optional default false 
 *	@param moveup boolean Moveup Flag [memory] optional default true
 *
 *	@return admin boolean Is admin [memory]
 *	@return level integer Web level [memory]
 *	@return grlevel integer Web Group Level [memory]
 *	@return masterkey long int Master key ID [memory]
 *	@return authorize string Authorization Control [memory]
 *	@return state string State [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ChainAuthorizeWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'chainid'),
			'optional' => array(
				'level' => 0, 
				'action' => 'edit', 
				'iaction' => 'edit', 
				'cstate' => false, 
				'mstate' => false, 
				'istate' => false, 
				'admin' => false, 
				'init' => true,
				'self' => false,
				'inherit' => 1,
				'errormsg' => 'Unable to Authorize',
				'custom' => false,
				'moveup' => true
			)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		
		/**
		 *	@initialize chain query
		**/
		$last = '';
		$args = array('chainid');
		$escparam = array();

		if($memory['cstate'] === true){
			$last = " and `state`<>'0' ";
		}
		else if($memory['cstate']){
			$last = " and `state`='\${cstate}' ";
			array_push($escparam, 'cstate');
			array_push($args, 'cstate');
		}	
		
		/**
		 *	@check chain info
		**/
		$workflow = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => $args,
			'conn' => 'cbconn',
			'relation' => '`chains`',
			'sqlprj' => '`masterkey`, `grroot`, `level`, `grlevel`, `authorize`, `state`',
			'sqlcnd' => "where `chainid`=\${chainid} $last",
			'escparam' => $escparam,
			'errormsg' => 'Invalid Chain ID'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0.masterkey' => 'masterkey', 'result.0.grroot' => 'grroot', 'result.0.level' => 'level', 'result.0.grlevel' => 'grlevel', 'result.0.authorize' => 'authorize', 'result.0.state' => 'state')
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
		
		$memory['msg'] = 'Key authorized successfully';
		
		/**
		 *	@check masterkey, authorize
		**/
		if(($memory['init'] || $memory['self']) && ($memory['keyid'] == $memory['masterkey'] || strpos($memory['authorize'], 'pb'.$memory['action']) !== false || (strpos($memory['authorize'], $memory['action']) === false && $memory['keyid'] > -1)))
			return $memory;
		
		if($memory['keyid'] < 0 && !$memory['admin']){
			$memory['valid'] = false;
			$memory['msg'] = 'Please login to continue';
			$memory['status'] = 407;
			$memory['details'] = 'Value -1 found for keyid @chain.authorize';
			return $memory;
		}
		
		/**
		 *	@custom checks
		**/
		if($memory['custom']){
			$memory = Snowblozm::execute($memory['custom'], $memory);
			if($memory['valid'])
				return $memory;
			$memory['valid'] = true;
		}
		
		/**
		 *	@read level
		**/
		if(strpos($memory['authorize'], 'gr'.$memory['action']) !== false){
			$level = $memory['grlevel'];
			$moveup = $level > -1;
			if(!$moveup) $level = -1 * $level;
			$memory['chainid'] = $memory['grroot'];
		}
		else {
			$level = $memory['level'];
			$moveup = $level > -1;
			$memory['level'] = $moveup ? $level + 1 : $level - 1;
			if(!$moveup) $level = -1 * $level;
		}
		
		/**
		 *	@check group
		**
		if(strpos($memory['authorize'], 'gr'.$memory['action']) !== false){
			$memory = Snowblozm::execute(array(
			array(
				'service' => 'transpera.relation.select.workflow',
				'args' => array('chainid', 'inherit', 'action'),
				'conn' => 'cbconn',
				'relation' => '`webs`',
				'sqlprj' => "`child`",
				'sqlcnd' => "where `parent`=\${chainid} and state='G' and `inherit`=\${inherit} and `control` like '%gr\${action}%'",
				'escparam' => array('action'),
				'check' => false,
				'errormsg' => 'No Group Found',
				'mapkey' => 'child'
			),
			array(
				'service' => 'cbcore.data.list.service',
				'args' => array('result'),
				'attr' => 'child',
				'mapname' => 'data',
				'default' => array(-1)
			)), $memory);
			
			$init = $memory['valid'] ? $memory['list'] : '(-1)';
			$level = $memory['grlevel'];
			$moveup = $level > -1;
			if(!$moveup) $level = -1 * $level;
		}
		else {
			$init = "(\${chainid})";
		}*/
		
		/**
		 *	@initialize chain query
		**/
		$last = $ilast = '';
		$args = array('keyid', 'chainid', 'action', 'iaction', 'inherit');
		$escparam = array('action', 'iaction');

		if($memory['mstate'] === true){
			$last = " and `state`<>'0' ";
		}
		else if($memory['mstate']){
			$last = " and `state`='\${mstate}' ";
			array_push($escparam, 'mstate');
			array_push($args, 'mstate');
		}		
		
		if($memory['istate'] === true){
			$ilast = " and `state`<>'0' ";
		}
		else if($memory['istate']){
			$ilast = " and `state`='\${istate}' ";
			array_push($escparam, 'istate');
			array_push($args, 'istate');
		}
		
		/**
		 *	@construct quthorize query
		**/
		$query = $memory['init'] ? "exists (select `chainid` from `members` where `chainid`=\${chainid} and `keyid`=\${keyid} and `control` like '%\${action}%' $last)" : 'false';
		
		$init = "(\${chainid})";
		$chain = "exists (select `chainid` from `members` where `chainid` in ";
		$chainend = " and `keyid`=\${keyid} and `control` like '%\${iaction}%' $ilast)";
		$child = $moveup ? "select `parent` from `webs` where `inherit`=\${inherit} and `control` like '%\${iaction}%' $ilast and `child` in " : "select `child` from `webs` where `inherit`=\${inherit} and `control` like '%\${iaction}%' $ilast and `parent` in ";
		
		while($level--){
			$init = '('.$child.$init.')';
			$query = $query.' or '.$chain.$init.$chainend;
		}
		
		/*$join = '`chainid` in ';
		$master = "(select `chainid` from `chains` where `masterkey`=\${keyid})";
		$chain = "(select `chainid` from `members` where `keyid`=\${keyid})";
		$child = 'select `child` from `webs` where `parent` in ';
	
		$query = $memory['init'] ? ($join.$master.' or '.$join.$chain) : '';
		
		while($level--){
			$chain = '('.$child.$chain.')';
			$master = '('.$child.$master.')';
			$query = $query.' or '.$join.$master.' or '.$join.$chain;
		}*/
		
		/**
		 *	@execute authorize query
		**/
		$service = array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => $args,
			'conn' => 'cbconn',
			'relation' => '`chains`',
			'sqlprj' => '`chainid`',
			'sqlcnd' => "where `chainid`=\${chainid} and ($query)",
			'escparam' => $escparam,
			'errstatus' => 403
		);
		//Snowblozm::$debug=true;
		$memory = Snowblozm::run($service, $memory);
		if($memory['admin'] && !$memory['valid']){
			$memory['admin'] = false;
			$memory['valid'] = true;
			$memory['msg'] = 'Successfully Executed';
			$memory['status'] = 200;
			$memory['details'] = 'Successfully executed';
		}
		//Snowblozm::$debug=false;
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('admin', 'level', 'grlevel', 'grroot', 'masterkey', 'authorize', 'state');
	}
	
}

?>