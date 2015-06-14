<?php 
require_once(SBSERVICE);

/**
 *	@class ReferenceGrantWorkflow
 *	@desc Manages granting of privileges to existing reference 
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string User [memory]
 *	@param id long int Reference ID [memory]
 *	@param childkeyid long int Key ID to be granted [memory]
 *	@param type string Type name [memory] optional default 'general'
 *	@param authorize string Parent control [memory] optional default 'edit:add:remove:list'
 *	@param control string Authorize control value [memory] optional default false='info:'.$authorize true=$authorize
 *	@param state string State value [memory] optional default 'A'
 *	@param path string Collation path [memory] optional default '/'
 *	@param leaf string Collation leaf [memory] optional default 'Child ID'
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
 *	@param verb string Activity verb [memory] optional default 'granted access of'
 *	@param join string Activity join [memory] optional default 'in'
 *	@param public integer Public log [memory] optional default 0
 *
 *	@return return id long int Chain member ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ReferenceGrantWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'id', 'childkeyid'),
			'optional' => array(
				'type' => 'general', 
				'authorize' => 'edit:add:remove:list', 
				'control' => false, 
				'state' => 'A', 
				'path' => '/', 
				'leaf' => false,
				'acstate' => true,
				'action' => 'edit', 
				'astate' => true, 
				'iaction' => 'edit', 
				'aistate' => true,
				'authinh' => 1,
				'autherror' => 'Unable to Authorize',
				'cname' => '',
				'pname' => '',
				'verb' => 'granted access of',
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
		$memory['msg'] = 'Reference privilege granted successfully';
		
		$workflow = array(
		array(
			'service' => 'guard.reference.authorize.workflow'
		),
		array(
			'service' => 'guard.member.add.workflow',
			'input' => array('chainid' => 'id', 'keyid' => 'childkeyid')
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
		return array('id');
	}
	
}

?>