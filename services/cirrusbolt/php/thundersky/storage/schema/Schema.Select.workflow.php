<?php 
require_once(SBSERVICE);

/**
 *	@class SchemaSelectWorkflow
 *	@desc Removes schema by ID
 *
 *	@param schid long int Schema ID [memory]
 *	@param schkey string Schema Initkey [memory] optional default 'schconn'
 *	@param keyid long int Usage Key ID [memory]
 *	@param cntrid long int Container ID [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SchemaSelectWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'schid'),
			'optional' => array('cntrid' => 0, 'schkey' => 'schconn')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Schema selected successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'schid'),
			'action' => 'edit'
		),
		array(
			'service' => 'store.schema.info.workflow'
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		
		if(!$memory['valid'])
			return $memory;
		
		$schema = $memory['schema'];
		Snowblozm::init($memory['schkey'], array(
			'type' => $schema['schtype'],
			'host' => $schema['schhost'],
			'user' => $schema['schname'],
			'pass' => $schema['schpass'],
			'database' => $schema['schname'],
		));
		
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>