<?php 
require_once(SBSERVICE);

/**
 *	@class SchemaInfoWorkflow
 *	@desc Returns schema information by ID
 *
 *	@param schid long int Schema ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param cntrid long int Container ID [memory] optional default 0
 *	@param cntrname string Container name [memory] optional default ''
 *
 *	@return schema array Schema information [memory]
 *	@return schid long integer Schema ID [memory]
 *	@return schname string Schema name [memory]
 *	@return cntrname string Container name [memory]
 *	@return cntrid long int Container ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SchemaInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('schid'),
			'optional' => array('keyid' => false, 'cntrname' => '', 'cntrid' => 0)
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Schema information given successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'schid', 'parent' => 'cntrid'),
			'conn' => 'cbconn',
			'relation' => '`schemas`',
			'sqlprj' => '`schid`, `schname`, `schpass`, `schhost`, `schtype`',
			'sqlcnd' => "where `schid`=\${id}",
			'errormsg' => 'Invalid Schema ID',
			'output' => array('entity' => 'schema')
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('schema'),
			'params' => array('schema.schname' => 'schname', 'schema.schtype' => 'schtype', 'schema.schhost' => 'schhost')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('schema', 'schid', 'cntrname', 'cntrid', 'admin', 'schname', 'schtype', 'schhost');
	}
	
}

?>