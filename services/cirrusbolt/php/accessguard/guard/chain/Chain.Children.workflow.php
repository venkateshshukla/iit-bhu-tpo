<?php 
require_once(SBSERVICE);

/**
 *	@class ChainChildrenWorkflow
 *	@desc Returns chain information
 *
 *	@param parent long int Parent ID [memory]
 *	@param type string Type name [memory] optional default 'general'
 *
 *	@return chains array Chains information [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ChainChildrenWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('parent'),
			'optional' => array('type' => 'general')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Chain information returned successfully';
		
		$service = array(
			'service' => 'transpera.relation.select.workflow',
			'args' => array('parent'),
			'conn' => 'cbconn',
			'relation' => '`chains`',
			'sqlprj' => '`chainid`, `authorize`, `state`',
			'sqlcnd' => "where `parent`=\${parent} and `type`='\${type}",
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