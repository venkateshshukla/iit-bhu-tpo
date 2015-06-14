<?php 
require_once(SBSERVICE);

/**
 *	@class ChainLinkWorkflow
 *	@desc Adds and removes chain links
 *
 *	@param chainid long int Chain ID [memory]
 *	@param remove boolean Is remove [memory] optional default false
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ChainLinkWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('chainid'),
			'optional' => array('remove' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Chain link managed successfully';
		$op = $memory['op'] ? '-' : '+';
		
		$service = array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('chainid'),
			'conn' => 'cbconn',
			'relation' => '`chains`',
			'sqlcnd' => "set `links`=`links`".$op."1, `mtime`=now() where `chainid`=\${chainid}",
			'errormsg' => 'Invalid Chain ID'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>