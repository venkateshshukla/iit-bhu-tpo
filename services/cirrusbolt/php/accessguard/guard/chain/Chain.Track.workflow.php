<?php 

require_once(SBSERVICE);

/**
 *	@class ChainTrackWorkflow
 *	@desc Tracks chain activity
 *
 *	@param child long int Child ID [memory]
 *	@param cname string Child name [memory] optional default ''
 *	@param parent long int Parent ID [memory] optional default -1
 *	@param pname string Parent name [memory] optional default ''
 *	@param keyid long int Key ID [memory]
 *	@param user string Key Username [memory]
 *	@param action string Action value [memory] optional default 'info'
 *	@param type string Type name [memory] optional default 'general'
 *	@param verb string Activity verb [memory] optional default 'viewed'
 *	@param join string Activity join [memory] optional default 'in'
 *	@param public integer Public log [memory] optional default 1
 *	@param multiple boolean Is multiple [memory] optional default false
 *
 *	@return return id long int Track ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ChainTrackWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('child', 'keyid', 'user'),
			'optional' => array('cname' => '', 'parent' => -1, 'pname' => '', 'action' => 'info', 'verb' => 'viewed', 'join' => 'in', 'type' => 'general', 'public' => 1, 'multiple' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Chain Activity Tracked Successfully';
		
		$memory['ipaddr'] = $_SERVER['REMOTE_ADDR'];
		if(isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
			$memory['ipaddr'] .= " / ".$_SERVER["HTTP_X_FORWARDED_FOR"];
		
		$memory['server'] = json_encode(array(
			'query' => isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '',
			'agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
			'host' => isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : '',
			'port' => isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : '',
			'request' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''
		));
		
		$escparam = array('user', 'action', 'type', 'cname', 'pname', 'verb', 'join', 'ipaddr', 'server');
		
		if($memory['multiple']){
			$qry = "select \${parent}, `chainid`, \${keyid}, '\${user}', '\${action}', '\${type}', substring('\${cname}', 0, 200), substring('\${pname}', 0, 200), '\${verb}', '\${join}', '\${ipaddr}', \${public}, now(), '\${server}' from `chains` where `chainid` in \${child}";
			array_push($escparam, 'child');
		}
		else {
			$qry = " values (\${parent}, \${child}, \${keyid}, '\${user}', '\${action}', '\${type}', substring('\${cname}', 0, 200), substring('\${pname}', 0, 200), '\${verb}', '\${join}', '\${ipaddr}', \${public}, now(), '\${server}')";
		}
		
		$service = array(
			'service' => 'transpera.relation.insert.workflow',
			'args' => array('parent', 'child', 'keyid', 'user', 'action', 'type', 'cname', 'pname', 'verb', 'join', 'ipaddr', 'server', 'public'),
			'conn' => 'cbconn',
			'relation' => '`tracks`',
			'sqlcnd' => "(`parent`, `child`, `keyid`, `user`, `action`, `type`, `cname`, `pname`, `verb`, `join`, `ipaddr`, `public`, `ttime`, `server`) $qry",
			'escparam' => $escparam
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('id');
	}
	
}

?>
