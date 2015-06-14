<?php 
require_once(SBSERVICE);

/**
 *	@class SchemaRemoveWorkflow
 *	@desc Removes schema by ID
 *
 *	@param schid long int Schema ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param cntrid long int Container ID [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SchemaRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'schid'),
			'optional' => array('cntrid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Schema removed successfully';
		
		$workflow = array(
		array(
			'service' => 'store.schema.info.workflow'
		),
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'schid'),
			'action' => 'remove'
		),
		array(
			'service' => 'transpera.reference.remove.workflow',
			'input' => array('parent' => 'cntr', 'id' => 'schid')
		),
		array(
			'service' => 'rdbms.database.drop.workflow',
			'input' => array('dbname' => 'schname'),
			'conn' => 'adconn'
		),
		array(
			'service' => 'transpera.relation.delete.workflow',
			'args' => array('schid'),
			'conn' => 'cbconn',
			'relation' => '`schemas`',
			'sqlcnd' => "where `schid`=\${schid}",
			'errormsg' => 'Invalid Schema ID'
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>