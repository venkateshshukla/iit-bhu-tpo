<?php 
require_once(SBSERVICE);

/**
 *	@class SchemaListWorkflow
 *	@desc Returns all schema information in container
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param cntrid long int Container ID [memory] optional default 0
 *	@param cntrname string Container name [memory] optional default ''
 *	@param selsvc string Select Service [memory] optional default 'griddata.schema.select'
  *	@param seltpl string Select Template [memory] optional default 'tpl-sch-sel'
 *
 *	@return schemas array Schemas information [memory]
 *	@return cntrid long int Container ID [memory]
 *	@return cntrname string Container name [memory]
 *	@return admin integer Is admin [memory]
 *	@return selsvc string Select Service [memory] 
  *	@return seltpl string Select Template [memory] 
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SchemaListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('cntrid' => 0, 'cntrname' => '', 'selsvc' => 'griddata.schema.select', 'seltpl' => 'tpl-sch-sel')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'cntrid'),
			'conn' => 'cbconn',
			'relation' => '`schemas`',
			'sqlprj' => '`schid`, `schname`, `schhost`, `schtype`',
			'sqlcnd' => "where `schid` in \${list}",
			'output' => array('entities' => 'schemas'),
			'successmsg' => 'Schemas information given successfully'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('schemas', 'cntrid', 'cntrname', 'admin', 'selsvc', 'seltpl');
	}
	
}

?>