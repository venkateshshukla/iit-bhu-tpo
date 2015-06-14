<?php 
require_once(SBSERVICE);

/**
 *	@class SchemaEditWorkflow
 *	@desc Edits schema using ID
 *
 *	@param schid long int Schema ID [memory]
 *	@param schpass string Schema password [memory]
 *	@param schhost string Schema host [memory] optional default 'localhost'
 *	@param schtype string Schema type [memory] optional default 'mysql' ('mysql')
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SchemaEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'schid', 'schpass', 'schhost', 'schtype')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Schema edited successfully';
		
		$workflow = array(
		array(
			'service' => 'store.schema.info.workflow',
			'output' => array('schpass' =>'oldpass', 'schhost' => 'oldhost', 'schtype' => 'oldtype')
		),
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'schid')
		),
		array(
			'service' => 'rdbms.database.password.workflow',
			'input' => array('dbname' => 'schname', 'dbpass' => 'schpass'),
			'conn' => 'adconn'
		),
		array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('schid', 'schpass', 'schhost', 'schtype'),
			'conn' => 'cbconn',
			'relation' => '`schemas`',
			'sqlcnd' => "set `schpass`='\${schpass}', `schhost`='\${schhost}', `schtype`='\${schtype}' where `schid`=\${schid}",
			'escparam' => array('schpass', 'schhost', 'schtype')
		),
		array(
			'service' => 'gauge.track.write.workflow',
			'input' => array('id' => 'schid')
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