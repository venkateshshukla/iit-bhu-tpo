<?php 
require_once(SBSERVICE);

/**
 *	@class ChainInfoWorkflow
 *	@desc Returns chain information
 *
 *	@param chainid long int Chain ID [memory]
 *	@param keyid long int Key ID [memory]
 *
 *	@return chain array Chain data information [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ChainInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('chainid', 'keyid')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Chain information returned successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('chainid', 'keyid'),
			'conn' => 'cbconn',
			'relation' => '`chains`',
			'sqlprj' => "`authorize`, `state`, `parent`, `user`, `count`, `author`, UNIX_TIMESTAMP(`wtime`)*1000 as `mtime`, if( exists (select `child` from `tracks` where `child`=\${chainid} and `keyid`=\${keyid} limit 1), 1, 0) as `read`",
			'sqlcnd' => "where `chainid`=\${chainid}",
			'errormsg' => 'Invalid Chain ID'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0' => 'chain')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('chain');
	}
	
}

?>