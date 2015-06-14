<?php 
require_once(SBSERVICE);

/**
 *	@class KeyRemoveWorkflow
 *	@desc Removes service key using ID
 *
 *	@param keyid long int Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class KeyRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Key removed successfully';
		
		$service = array(
			'service' => 'transpera.relation.delete.workflow',
			'args' => array('keyid'),
			'conn' => 'cbconn',
			'relation' => '`keys`',
			'sqlcnd' => "where `keyid`=\${keyid}",
			'errormsg' => 'Invalid Service Key ID'
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