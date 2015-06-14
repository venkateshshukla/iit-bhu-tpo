<?php 
require_once(SBSERVICE);

/**
 *	@class MemberAddWorkflow
 *	@desc Adds member key to Chain
 *
 *	@param keyid long int Key ID [memory]
 *	@param chainid long int Chain ID [memory]
 *	@param type string Type name [memory] optional default 'general'
 *	@param authorize string Parent control [memory] optional default 'edit:add:remove:list'
 *	@param control string Authorize control value [memory] optional default false='info:'.$authorize true=$authorize
 *	@param state string State value [memory] optional default 'A'
 *	@param path string Collation path [memory] optional default '/'
 *	@param leaf string Collation leaf [memory] optional default 'Child ID'
 *
 *	@return return id long int Member key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class MemberAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'chainid'),
			'optional' => array('type' => 'general', 'authorize' => 'edit:add:remove:list', 'control' => false, 'state' => 'A', 'path' => '/', 'leaf' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Member added successfully';
		$memory['leaf'] = $memory['leaf'] ? $memory['leaf'] : $memory['chainid'];
		$memory['control'] = $memory['control'] ? ($memory['control'] === true ? $memory['authorize'] : $memory['control']) : 'info:'.$memory['authorize'];
		
		$service = array(
			'service' => 'transpera.relation.insert.workflow',
			'args' => array('chainid', 'keyid', 'type', 'control', 'state', 'path', 'leaf'),
			'conn' => 'cbconn',
			'relation' => '`members`',
			'sqlcnd' => "(`chainid`, `keyid`, `user`, `type`, `control`, `state`, `path`, `leaf`, `ctime`) values (\${chainid}, \${keyid}, (select `user` from `keys` where `keyid`=\${keyid}), '\${type}', '\${control}', '\${state}', '\${path}', '\${leaf}', now())",
			'escparam' => array('type', 'control', 'state', 'path', 'leaf')
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