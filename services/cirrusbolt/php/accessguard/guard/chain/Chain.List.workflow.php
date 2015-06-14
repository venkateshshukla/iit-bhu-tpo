<?php 
require_once(SBSERVICE);

/**
 *	@class ChainListWorkflow
 *	@desc Returns chain information
 *
 *	@param chainid set of long int Chain ID [memory]
 *	@param keyid long int Key ID [memory]
 *
 *	@return chains array Chains information [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ChainListWorkflow implements Service {
	
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
		
		$service = array(
			'service' => 'transpera.relation.select.workflow',
			'args' => array('chainid', 'keyid'),
			'conn' => 'cbconn',
			'relation' => '`chains`',
			'sqlprj' => "`chainid`, `authorize`, `state`, `parent`, `user`, `author`, `count`, UNIX_TIMESTAMP(`wtime`)*1000 as `mtime`, if( exists (select `child` from `tracks` where `child`=`chainid` and `keyid`=\${keyid}), 1, 0) as `read`",
			'sqlcnd' => "where `chainid` in \${chainid}",
			'escparam' => array('chainid'),
			'errormsg' => 'Invalid Chain ID',
			'check' => false,
			'output' => array('result' => 'chains'),
			'mapkey' => 'chainid',
			'mapname' => 'chain'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('chains');
	}
	
}

?>