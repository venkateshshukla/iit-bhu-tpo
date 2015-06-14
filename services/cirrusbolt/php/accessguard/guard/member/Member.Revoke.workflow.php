<?php 
require_once(SBSERVICE);

/**
 *	@class MemberRevokeWorkflow
 *	@desc Removes member key from chain
 *
 *	@param user string Key User [memory]
 *	@param chainid long int Chain ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class MemberRevokeWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('user', 'chainid')
		);
	}

	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Member removed successfully';
		
		$service = array(
			'service' => 'transpera.relation.delete.workflow',
			'args' => array('user', 'chainid'),
			'conn' => 'cbconn',
			'relation' => '`members`',
			'sqlcnd' => "where `chainid`=\${chainid} and `keyid`=(select `keyid` from `keys` where `user`='\${user}')",
			'escparam' => array('user'),
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