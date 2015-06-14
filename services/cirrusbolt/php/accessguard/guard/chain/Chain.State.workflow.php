<?php 
require_once(SBSERVICE);

/**
 *	@class ChainStateWorkflow
 *	@desc Edits chain state control value
 *
 *	@param chainid long int/string Chain ID(s) [memory]
 *	@param state string State value [memory] optional default '0'
 *	@param multiple boolean Is multiple [memory] optional default false
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ChainStateWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('chainid'),
			'optional' => array('multiple' => false, 'state' => '0')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Chain state value edited successfully';
		$query = $memory['multiple'] ? ' in \${chainid}' : '=\${chainid}';
		$esc = $memory['multiple'] ? array('state', 'chainid') : array('state');
		
		$service = array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('chainid', 'state'),
			'conn' => 'cbconn',
			'relation' => '`chains`',
			'sqlcnd' => "set `state`='\${state}', `mtime`=now() where `chainid`$query",
			'errormsg' => 'Invalid Chain ID',
			'escparam' => $esc
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