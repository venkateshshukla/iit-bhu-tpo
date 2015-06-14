<?php 
require_once(SBSERVICE);

/**
 *	@class ReferenceLinkWorkflow
 *	@desc Manages linking of references
 *
 *	@param keyid long int Usage Key ID [memory] 
 *	@param user string User [memory]
 *	@param parent long int Reference ID [memory]
 *	@param id long int Reference ID [memory]
 *	@param authorize string Authorize control value [memory] optional default (inherit)
 *	@param icontrol string Authorize control value for web [memory] optional default false='info:'.(inherit) true=(inherit)
 *	@param inherit integer Is inherit [memory] optional default 0
 *	@param istate string State value for web [memory] optional default 'A'
 *	@param root string Collation root [memory] optional default '/masterkey'
 *	@param type string Type name [memory] optional default 'general'
 *	@param path string Collation path [memory] optional default '/'
 *	@param leaf string Collation leaf [memory] optional default 'Child ID'
 *
 *	@param acstate string State to authorize chain [memory] optional default true (false= All)
 *	@param action string Action to authorize member [memory] optional default 'list'
 *	@param astate string State to authorize member [memory] optional default true (false= All)
 *	@param iaction string Action to authorize inherit [memory] optional default 'list'
 *	@param aistate string State to authorize inherit [memory] optional default true (false= All)
 *
 *	@param cache boolean Is cacheable [memory] optional default true
 *	@param expiry int Cache expiry [memory] optional default 150
 *	@param authinh integer Check inherit [memory] optional default 1
 *	@param autherror string Error msg [memory] optional default 'Unable to Authorize'
 *
 *	@param cname string Child name [memory] optional default ''
 *	@param pname string Parent name [memory] optional default ''
 *	@param verb string Activity verb [memory] optional default 'linked'
 *	@param join string Activity join [memory] optional default 'to'
 *	@param public integer Public log [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ReferenceLinkWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'parent', 'id'),
			'optional' => array(
				'root' => false, 
				'type' => 'general', 
				'path' => '/', 
				'leaf' => false, 
				'authorize' => false, 
				'inherit' => 0, 
				'control' => false, 
				'state' => 'A', 
				'icontrol' => false, 
				'istate' => 'A',
				'acstate' => true,
				'action' => 'list', 
				'astate' => true, 
				'iaction' => 'list', 
				'aistate' => true,
				'authinh' => 1,
				'autherror' => 'Unable to Authorize',
				'cname' => '',
				'pname' => '',
				'verb' => 'linked',
				'join' => 'to',
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
		$memory['msg'] = 'Reference linked successfully';
		$authorize = $memory['authorize'] ? 'inhauthorize' : 'authorize';
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'parent'),
			'output' => array('authorize' => $authorize)
		),
		array(
			'service' => 'guard.web.add.workflow',
			'input' => array('child' => 'id'),
			'output' => array('id' => 'webid')
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