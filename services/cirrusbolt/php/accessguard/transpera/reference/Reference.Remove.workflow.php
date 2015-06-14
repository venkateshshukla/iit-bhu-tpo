<?php 
require_once(SBSERVICE);

/**
 *	@class ReferenceRemoveWorkflow
 *	@desc Manages removal of existing reference 
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string User [memory]
 *	@param id long int Reference ID [memory]
 *	@param parent long int Reference ID [memory]
 *	@param type string Type name [memory] optional default 'general'
 *
 *	@param acstate string State to authorize chain [memory] optional default true (false= All)
 *	@param action string Action to authorize member [memory] optional default 'remove'
 *	@param astate string State to authorize member [memory] optional default true (false= All)
 *	@param iaction string Action to authorize inherit [memory] optional default 'remove'
 *	@param aistate string State to authorize inherit [memory] optional default true (false= All)
 *
 *	@param cache boolean Is cacheable [memory] optional default true
 *	@param expiry int Cache expiry [memory] optional default 150
 *	@param authinh integer Check inherit [memory] optional default 1
 *	@param autherror string Error msg [memory] optional default 'Unable to Authorize'
 *
 *	@param cname string Child name [memory] optional default ''
 *	@param pname string Parent name [memory] optional default ''
 *	@param verb string Activity verb [memory] optional default 'deleted'
 *	@param join string Activity join [memory] optional default 'from'
 *	@param public integer Public log [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ReferenceRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'parent', 'id'),
			'optional' => array(
				'type' => 'general',
				'acstate' => true,
				'action' => 'remove', 
				'astate' => true, 
				'iaction' => 'remove', 
				'aistate' => true,
				'authinh' => 1,
				'autherror' => 'Unable to Authorize',
				'cname' => '',
				'pname' => '',
				'verb' => 'deleted',
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
		$memory['msg'] = 'Reference deleted successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			//'input' => array('id' => 'parent')
		),
		array(
			'service' => 'guard.chain.remove.workflow',
			'input' => array('chainid' => 'id')
		),
		array(
			'service' => 'guard.member.delete.workflow',
			'input' => array('chainid' => 'id')
		),
		array(
			'service' => 'guard.web.remove.workflow',
			'input' => array('child' => 'id')
		),
		array(
			'service' => 'guard.chain.count.workflow',
			'input' => array('chainid' => 'parent'),
			'remove' => true
		),
		array(
			'service' => 'guard.chain.track.workflow',
			'input' => array('child' => 'id'),
			'output' => array('id' => 'trackid')
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