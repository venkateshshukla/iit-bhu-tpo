<?php 
require_once(SBSERVICE);

/**
 *	@class WebSetWorkflow
 *	@desc Sets state of owner in web
 *
 *	@param owner long int Key ID [memory]
 *	@param state string State [memory] optional default '0'
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class WebSetWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('owner'),
			'optional' => array('state' => '0')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Web set successfully';
		
		$service = array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('owner', 'state'),
			'conn' => 'cbconn',
			'relation' => '`webs`',
			'sqlcnd' => "set `state`='\${state}' where `child` in (select `chainid` from `members` where `keyid`=\${owner})",
			'escparam' => array('state'),
			'check' => false,
			'errormsg' => 'Invalid Key ID'
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