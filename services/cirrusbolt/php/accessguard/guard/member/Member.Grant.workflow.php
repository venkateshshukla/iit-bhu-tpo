<?php 
require_once(SBSERVICE);

/**
 *	@class MemberGrantWorkflow
 *	@desc Adds member key to Chain
 *
 *	@param member string Key User [memory]
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
class MemberGrantWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('member', 'chainid'),
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
			'args' => array('chainid', 'member', 'type', 'control', 'state', 'path', 'leaf'),
			'conn' => 'cbconn',
			'relation' => '`members`',
			'sqlcnd' => "(`chainid`, `keyid`, `user`, `type`, `control`, `state`, `path`, `leaf`, `ctime`) values (\${chainid}, (select `keyid` from `keys` where `user`='\${member}'), '\${member}', '\${type}', '\${control}', '\${state}', '\${path}', '\${leaf}', now())",
			'escparam' => array('member', 'type', 'control', 'state', 'path', 'leaf'),
			'errormsg' => 'Invalid Username'
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