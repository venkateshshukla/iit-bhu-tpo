<?php 
require_once(SBSERVICE);

/**
 *	@class PersonRemoveWorkflow
 *	@desc Removes person by ID
 *
 *	@param pnid long int Person ID [memory]
 *	@param peopleid long int People ID [memory] optional default PEOPLE_ID
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PersonRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'pnid'),
			'optional' => array('peopleid' => PEOPLE_ID)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Person removed successfully';
		
		$workflow = array(
		array(
			'service' => 'people.person.info.workflow'
		),
		array(
			'service' => 'transpera.reference.delete.workflow',
			'input' => array('parent' => 'peopleid', 'id' => 'pnid'),
			'type' => 'person',
		),
		array(
			'service' => 'transpera.relation.delete.workflow',
			'args' => array('pnid'),
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'type' => 'person',
			'sqlcnd' => "where `pnid`=\${pnid}",
			'errormsg' => 'Invalid Person ID',
			'successmsg' => 'Person removed successfully',
		),
		/*array(
			'service' => 'storage.file.remove.workflow',
			'input' => array('fileid' => 'thumbnail'),
			'dirid' => PERSON_THUMB
		)*/);
		
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