<?php 
require_once(SBSERVICE);

/**
 *	@class ChainWriteWorkflow
 *	@desc Updates data for writing chain
 *
 *	@param chainid long int Chain ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ChainWriteWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('chainid')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){		
		$memory['msg'] = 'Chain written successfully';
		
		$service = array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('chainid'),
			'conn' => 'cbconn',
			'relation' => '`chains`',
			'sqlcnd' => "set `writes`=`writes`+1, `wtime`=now() where `chainid`=\${chainid}",
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