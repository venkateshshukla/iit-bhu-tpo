<?php 
require_once(SBSERVICE);

/**
 *	@class ChainControlWorkflow
 *	@desc Edits chain authorize control value
 *
 *	@param chainid long int/string Chain ID(s) [memory]
 *	@param authorize string Control value [memory]
 *	@param multiple boolean Is multiple [memory] optional default false
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ChainControlWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('chainid', 'authorize'),
			'optional' => array('multiple' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Chain control value edited successfully';
		$query = $memory['multiple'] ? ' in \${chainid}' : '=\${chainid}';
		$esc = $memory['multiple'] ? array('authorize', 'chainid') : array('authorize');
		
		$service = array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('chainid', 'authorize'),
			'conn' => 'cbconn',
			'relation' => '`chains`',
			'sqlcnd' => "set `authorize`='\${authorize}', `mtime`=now() where `chainid`$query",
			'errormsg' => 'Invalid Chain ID',
			'escparam' => array('authorize')
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