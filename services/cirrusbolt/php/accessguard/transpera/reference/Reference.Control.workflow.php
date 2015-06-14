<?php 
require_once(SBSERVICE);

/**
 *	@class ReferenceControlWorkflow
 *	@desc Manages editing of authorize control value of existing reference 
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string User [memory]
 *	@param id long int Reference ID [memory]
 *	@param authorize string Control value [memory]
 *	@param miltiple boolean Is multiple [memory] optional default false
 *
 *	@param acstate string State to authorize chain [memory] optional default true (false= All)
 *	@param action string Action to authorize member [memory] optional default 'edit'
 *	@param astate string State to authorize member [memory] optional default true (false= All)
 *	@param iaction string Action to authorize inherit [memory] optional default 'edit'
 *	@param aistate string State to authorize inherit [memory] optional default true (false= All)
 *
 *	@param cache boolean Is cacheable [memory] optional default true
 *	@param expiry int Cache expiry [memory] optional default 150
 *	@param authinh integer Check inherit [memory] optional default 1
 *	@param autherror string Error msg [memory] optional default 'Unable to Authorize'
 *
 *	@param cname string Child name [memory] optional default ''
 *	@param pname string Parent name [memory] optional default ''
 *	@param verb string Activity verb [memory] optional default 'updated authorization of'
 *	@param join string Activity join [memory] optional default 'in'
 *	@param public integer Public log [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ReferenceControlWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'id', 'authorize'),
			'optional' => array(
				'multiple' => false,
				'acstate' => true,
				'action' => 'edit', 
				'astate' => true, 
				'iaction' => 'edit', 
				'aistate' => true,
				'authinh' => 1,
				'autherror' => 'Unable to Authorize',
				'cname' => '',
				'pname' => '',
				'verb' => 'updated authorization of',
				'join' => 'in',
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
		$memory['msg'] = 'Reference control value edited successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('chainid' => 'id')
		),
		array(
			'service' => 'guard.chain.control.workflow',
			'input' => array('chainid' => 'id')
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