<?php 
require_once(SBSERVICE);

/**
 *	@class KeyResetWorkflow
 *	@desc Resets service key using ID for context
 *
 *	@param keyid long int Key ID [memory]
 *	@param context string Context [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class KeyResetWorkflow implements Service {
	
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
		$memory['msg'] = 'Key Resetted successfully';
		
		$service = array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('keyid', 'context'),
			'conn' => 'cbconn',
			'relation' => '`keys`',
			'sqlcnd' => "set `context`=replace(`context`, ':\${context}', '') where `keyid`=\${keyid}",
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