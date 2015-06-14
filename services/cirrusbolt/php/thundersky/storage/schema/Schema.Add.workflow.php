<?php 
require_once(SBSERVICE);

/**
 *	@class SchemaAddWorkflow
 *	@desc Adds new schema to container
 *
 *	@param schname string Schema name [memory]
 *	@param schpass string Schema password [memory]
 *	@param schhost string Schema host [memory] optional default 'localhost'
 *	@param schtype string Schema type [memory] optional default 'mysql' ('mysql')
 *	@param keyid long int Usage Key ID [memory]
 *	@param cntrid long int Container ID [memory] optional default 0
 *	@param level integer Web level [memory] optional default false (inherit container admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
 *
 *	@return schid long int Schema ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SchemaAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'schname', 'schpass'),
			'optional' => array('cntrid' => 0, 'level' => false, 'owner' => false, 'schtype' => 'mysql', 'schhost' => 'localhost')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Schema added successfully';
		$memory['owner'] = $memory['owner'] ? $memory['owner'] : $memory['keyid'];
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.add.workflow',
			'input' => array('parent' => 'cntrid'),
			'output' => array('id' => 'schid')
		),
		array(
			'service' => 'rdbms.database.create.workflow',
			'input' => array('dbname' => 'schname', 'dbpass' => 'schpass'),
			'conn' => 'adconn'
		),
		array(
			'service' => 'transpera.relation.insert.workflow',
			'args' => array('schid', 'owner', 'schname', 'schpass', 'schhost', 'schtype'),
			'conn' => 'cbconn',
			'relation' => '`schemas`',
			'sqlcnd' => "(`schid`, `owner`, `schname`, `schpass`, `schhost`, `schtype`) values (\${schid}, \${owner}, '\${schname}', '\${schpass}', '\${schhost}', '\${schtype}')",
			'escparam' => array('schname', 'schpass', 'schhost', 'schtype')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('schid');
	}
	
}

?>