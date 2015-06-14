<?php 
require_once(SBSERVICE);

/**
 *	@class ReferenceAddWorkflow
 *	@desc Manages addition of new reference 
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param parent long int Reference ID [memory]
 *	@param level integer Web level [memory] optional default (inherit)
 *	@param grlevel integer Group level [memory] optional default (inherit) 0
 *	@param grroot integer Group root [memory] optional default (inherit) 0
 *	@param owner long int Owner Key ID [memory] optional default keyid
 *	@param authorize string Authorize control value [memory] optional default (inherit)
 *	@param control string Authorize control value for member [memory] optional default false='info:'.(inherit) true=(inherit)
 *	@param icontrol string Authorize control value for web [memory] optional default false='info:'.(inherit) true=(inherit)
 *	@param inherit integer Is inherit [memory] optional default 1
 *	@param state string State value for member [memory] optional default 'A'
 *	@param istate string State value for web [memory] optional default 'A'
 *	@param root string Collation root [memory] optional default '/masterkey'
 *	@param user string Username [memory] optional default 'unknown@reference.add'
 *	@param type string Type name [memory] optional default 'general'
 *	@param path string Collation path [memory] optional default '/'
 *	@param leaf string Collation leaf [memory] optional default 'Child ID'
 *
 *	@param cache boolean Is cacheable [memory] optional default true
 *	@param expiry int Cache expiry [memory] optional default 150
 *
 *	@param acstate string State to authorize chain [memory] optional default true (false= None)
 *	@param action string Action to authorize member [memory] optional default 'add'
 *	@param astate string State to authorize member [memory] optional default true (false= None)
 *	@param iaction string Action to authorize inherit [memory] optional default 'add'
 *	@param aistate string State to authorize inherit [memory] optional default true (false= None)
 *
 *	@param authinh integer Check inherit [memory] optional default 1
 *	@param autherror string Error msg [memory] optional default 'Unable to Authorize'
 *
 *	@param cname string Child name [memory] optional default ''
 *	@param pname string Parent name [memory] optional default ''
 *	@param verb string Activity verb [memory] optional default 'added'
 *	@param join string Activity join [memory] optional default 'to'
 *	@param public integer Public log [memory] optional default 1
 *
 *	@return return id long int Reference ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ReferenceAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'parent'),
			'optional' => array(
				'level' => false, 
				'grlevel' => 0,
				'grroot' => 0,
				'owner' => false, 
				'root' => false, 
				'user' => 'unknown@reference.add',
				'type' => 'general', 
				'path' => '/', 
				'leaf' => false, 
				'authorize' => false, 
				'inherit' => 1, 
				'control' => false, 
				'state' => 'A', 
				'icontrol' => false, 
				'istate' => 'A',
				'cstate' => true,
				'action' => 'add', 
				'astate' => true, 
				'iaction' => 'add', 
				'aistate' => true,
				'authinh' => 1,
				'autherror' => 'Unable to Authorize',
				'cname' => '',
				'pname' => '',
				'verb' => 'added',
				'join' => 'to',
				'public' => 1,
				'cache' => true,
				'expiry' => 150
			)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['owner'] = $memory['owner'] ? $memory['owner'] : $memory['keyid'];
		$memory['msg'] = 'Reference added successfully';
		$grroot = $memory['grroot'] ? 'inhgrroot' : 'grroot';
		$level = $memory['level'] ? 'inhlevel' : 'level';
		$grlevel = $memory['grlevel'] ? 'inhlevel' : 'grlevel';
		$authorize = $memory['authorize'] ? 'inhauthorize' : 'authorize';
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'parent'),
			'output' => array('level' => $level, 'authorize' => $authorize, 'grroot' => $grroot, 'grlevel' => $grlevel)
		),
		array(
			'service' => 'guard.chain.add.workflow',
			'input' => array('masterkey' => 'owner')
		),
		array(
			'service' => 'guard.member.add.workflow',
			'input' => array('keyid' => 'owner', 'chainid' => 'id'),
			'output' => array('id' => 'memid')
		),
		array(
			'service' => 'guard.web.add.workflow',
			'input' => array('child' => 'id'),
			'output' => array('id' => 'webid')
		),
		array(
			'service' => 'guard.chain.count.workflow',
			'input' => array('chainid' => 'parent')
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