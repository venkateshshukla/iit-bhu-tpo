<?php 
require_once(SBSERVICE);

/**
 *	@class MemberRemoveWorkflow
 *	@desc Removes member key from chain
 *
 *	@param keyid long int Key ID [memory]
 *	@param chainid long int Chain ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class MemberRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'chainid')
		);
	}

	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Member removed successfully';
		
		$service = array(
			'service' => 'transpera.relation.delete.workflow',
			'args' => array('keyid', 'chainid'),
			'conn' => 'cbconn',
			'relation' => '`members`',
			'sqlcnd' => "where `chainid`=\${chainid} and `keyid`=\${keyid}",
			'errormsg' => 'Invalid Member'
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