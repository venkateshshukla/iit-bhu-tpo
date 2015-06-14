<?php 
require_once(SBSERVICE);

/**
 *	@class MemberStateWorkflow
 *	@desc Edits chain state control value
 *
 	@param keyid long int Key ID [memory]
 *	@param chainid long int Chain ID [memory]
 *	@param state string State value [memory] optional default '0'
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class MemberStateWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'chainid'),
			'optional' => array('state' => '0')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Member state value edited successfully';
		
		$service = array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('keyid', 'chainid', 'state'),
			'conn' => 'cbconn',
			'relation' => '`members`',
			'sqlcnd' => "set `state`='\${state}' where `chainid`=\${chainid} and `keyid`=\${keyid}",
			'errormsg' => 'Invalid Member ID',
			'escparam' => array('state')
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