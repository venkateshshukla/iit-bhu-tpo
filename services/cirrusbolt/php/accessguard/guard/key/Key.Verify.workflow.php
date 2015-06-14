<?php 
require_once(SBSERVICE);

/**
 *	@class KeyVerifyWorkflow
 *	@desc Verifies service key using ID for context
 *
 *	@param keyid long int Key ID [memory]
 *	@param context string Context [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class KeyVerifyWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'context')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Key verified successfully';
		
		$service = array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('context', 'keyid'),
			'conn' => 'cbconn',
			'relation' => '`keys`',
			'sqlcnd' => "set `context`=concat(`context`, ':\${context}') where `keyid`=\${keyid}",
			'escparam' => array('context'),
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