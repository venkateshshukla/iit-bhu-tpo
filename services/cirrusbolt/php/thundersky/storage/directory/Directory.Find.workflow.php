<?php 
require_once(SBSERVICE);

/**
 *	@class DirectoryFindWorkflow
 *	@desc Returns information for directory using Name
 *
 *	@param name long int Directory name [memory]
 *
 *	@return dirid string Directory ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class DirectoryFindWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Directory found successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('name'),
			'conn' => 'cbsconn',
			'relation' => '`directories`',
			'sqlcnd' => "where `name`='\${name}'",
			'escparam' => array('name'),
			'errormsg' => 'Invalid Directory ID'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0.dirid' => 'dirid')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('dirid');
	}
	
}

?>