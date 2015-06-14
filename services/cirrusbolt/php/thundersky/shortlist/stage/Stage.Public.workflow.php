<?php 
require_once(SBSERVICE);

/**
 *	@class StagePublicWorkflow
 *	@desc Edits stage public status for shortlist using ID
 *
 *	@param stageid long int Stage ID [memory]
 *	@param open integer Is open [memory] optional default 0
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class StagePublicWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'stageid'),
			'optional' => array('open' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Stage public status edited successfully';
		$memory['authorize'] = $memory['open'] ? 'edit:child:list' : 'edit:child:list:qualify';
		
		$workflow = array(
		array(
			'service' => 'shortlist.stage.selection.workflow'
		),
		array(
			'service' => 'cbcore.data.list.service',
			'args' => array('selections'),
			'attr' => 'selid'
		),
		array(
			'service' => 'transpera.reference.control.workflow',
			'input' => array('id' => 'list')
			'multiple' => true
		),
		array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('stageid', 'open'),
			'conn' => 'cbslconn',
			'relation' => '`stages`',
			'sqlcnd' => "set `open`=\${open} where `stageid`=\${stageid}"
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