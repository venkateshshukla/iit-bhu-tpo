<?php 
require_once(SBSERVICE);

/**
 *	@class EntityRemoveWorkflow
 *	@desc Removes entity by ID
 *
 *	@param relation string Relation name [memory]
 *	@param sqlcnd string SQL condition [memory]
 *	@param user string Username [memory] optional default 'unknown@entity.remove'
 *	@param errormsg string Error message [memory] optional default 'Invalid Entity ID'
 *	@param successmsg string Success message [memory] optional default 'Entity information successfully'
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param id long int Reference ID [memory]
 *	@param parent long int Reference ID [memory]
 *	@param type string Type name [memory] optional default 'general'
 *
 *	@param acstate string State to authorize chain [memory] optional default true (false= All)
 *	@param action string Action to authorize member [memory] optional default 'remove'
 *	@param astate string State to authorize member [memory] optional default true (false= None)
 *	@param iaction string Action to authorize inherit [memory] optional default 'remove'
 *	@param aistate string State to authorize inherit [memory] optional default true (false= None)
 *	@param authinh integer Check inherit [memory] optional default 1
 *	@param autherror string Error msg [memory] optional default 'Unable to Authorize'
 *
 *	@param destruct array Destruction Workflow [memory] optional default false
 *
 *	@param sacstate string State to authorize chain [memory] optional default true (false= All)
 *	@param saction string Action to authorize [memory] optional default 'edit'
 *	@param sastate string State to authorize member [memory] optional default true (false= All)
 *	@param siaction string Action to authorize inherit [memory] optional default 'edit'
 *	@param saistate string State to authorize inherit [memory] optional default true (false= All)
 *	@param sinit boolean init flag [memory] optional default true
 *	@param sauthinh integer Check inherit [memory] optional default 1
 *	@param sautherror string Error msg [memory] optional default 'Unable to Authorize'
 *
 *	@param cname string Child name [memory] optional default ''
 *	@param pname string Parent name [memory] optional default ''
 *	@param verb string Activity verb [memory] optional default 'removed'
 *	@param join string Activity join [memory] optional default 'from'
 *	@param public integer Public log [memory] optional default 0
 *
 *	@param conn array DataService instance configuration key [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class EntityRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('conn', 'keyid', 'id', 'relation', 'sqlcnd'),
			'optional' => array(
				'user' => 'unknown@entity.remove',
				'parent' => 0, 
				'type' => 'general',
				'acstate' => true,
				'action' => 'remove', 
				'astate' => true, 
				'iaction' => 'remove', 
				'aistate' => true,
				'authinh' => 1,
				'autherror' => 'Unable to Authorize',
				'sacstate' => true,
				'saction' => 'remove', 
				'sastate' => true, 
				'siaction' => 'remove', 
				'saistate' => true, 
				'sinit' => true,
				'sauthinh' => 1,
				'sautherror' => 'Unable to Authorize',
				'successmsg' => 'Entity removed successfully', 
				'errormsg' => 'Invalid Entity ID', 
				'destruct' => false,
				'cname' => '',
				'pname' => '',
				'verb' => 'removed',
				'join' => 'from',
				'public' => 0,
				'cache' => true,
				'expiry' => 150
			)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = $memory['successmsg'];
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow'
		));
		
		if($memory['destruct']){
			foreach($memory['destruct'] as $destruct)
				array_push($workflow, $destruct);
		}
		
		array_push($workflow,
		array(
			'service' => 'transpera.reference.remove.workflow',
			'input' => array(
				'acstate' => 'sacstate', 
				'action' => 'saction', 
				'astate' => 'sastate', 
				'iaction' => 'siaction', 
				'iastate' => 'siastate', 
				'init' => 'sinit',
				'authinh' => 'sauthinh',
				'autherror' => 'sautherror',
			)
		),
		array(
			'service' => 'transpera.relation.delete.workflow',
			'args' => array('id')
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