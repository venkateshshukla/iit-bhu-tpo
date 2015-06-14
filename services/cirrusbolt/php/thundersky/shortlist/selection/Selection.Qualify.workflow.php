<?php 
require_once(SBSERVICE);

/**
 *	@class SelectionQualifyWorkflow
 *	@desc Qualifies stage for selection of shortlist if not frozen
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param selid long int Selection ID [memory]
 *	@param owner long int Owner ID [memory] optional default keyid
 *	@param qualify boolean Is qualify [memory] optional default true
 *
 *	@param result long int Stage ID to which qualified [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SelectionQualifyWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'selid', ),
			'optional' => array('qualify' => true, 'owner' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Selection '.($memory['qualify'] ? 'Approved' : 'Rejected').' Successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'selid'),
			'conn' => 'cbslconn',
			'relation' => '`selections`',
			'sqlcnd' => "where `selid`=\${id}",
			'action' => 'qualify',
			'iaction' => 'add',
			'errormsg' => 'Invalid Selection to qualify', 
			'output' => array('entity' => 'selection')
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('selection'),
			'params' => array('selection.stageid' => 'stageid', 'selection.refer' => 'refer')
		),
		array(
			'service' => 'score.stage.adjacent.workflow',
			'input' => array('high' => 'qualify')
		),
		array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('selid', 'adjacent'),
			'conn' => 'cbslconn',
			'relation' => '`selections`',
			'sqlcnd' => "set `stageid`=\${adjacent} where `selid`=\${selid} and `status`>0",
			'errormsg' => 'Selection already rejected'
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