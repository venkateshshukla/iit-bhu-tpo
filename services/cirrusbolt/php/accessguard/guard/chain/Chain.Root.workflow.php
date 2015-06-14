<?php 
require_once(SBSERVICE);

/**
 *	@class ChainRootWorkflow
 *	@desc Edits chain collation root value
 *
 *	@param chainid long int Chain ID [memory]
 *	@param root string Collation root [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ChainRootWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('chainid', 'root')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Chain collation root edited successfully';
		
		$service = array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('chainid', 'root'),
			'conn' => 'cbconn',
			'relation' => '`chains`',
			'sqlcnd' => "set `root`='\${root}', `mtime`=now() where `chainid`=\${chainid}",
			'errormsg' => 'Invalid Chain ID',
			'escparam' => array('root')
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