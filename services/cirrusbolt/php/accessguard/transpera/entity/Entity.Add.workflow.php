<?php 
require_once(SBSERVICE);

/**
 *	@class EntityAddWorkflow
 *	@desc Adds new entity
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param parent long int Reference ID [memory]
 *	@param level integer Web level [memory] optional default (inherit)
 *	@param grlevel integer Group level [memory] optional default 0
 *	@param grroot integer Group root [memory] optional default (inherit) 0
 *	@param owner long int Owner Key ID [memory] optional default keyid
 *	@param authorize string Authorize control value [memory] optional default (inherit)
 *	@param control string Authorize control value for member [memory] optional default false='info:'.(inherit) true=(inherit)
 *	@param icontrol string Authorize control value for web [memory] optional default false='info:'.(inherit) true=(inherit)
 *	@param inherit integer Is inherit [memory] optional default 1
 *	@param state string State value for member [memory] optional default 'A'
 *	@param istate string State value for web [memory] optional default 'A'
 *	@param user string Username [memory] optional default 'unknown@entity.add'
 *	@param root string Collation root [memory] optional default '/masterkey'
 *	@param type string Type name [memory] optional default 'general'
 *	@param path string Collation path [memory] optional default '/'
 *	@param leaf string Collation leaf [memory] optional default 'Child ID'
 *
 *	@param acstate string State to authorize chain [memory] optional default true (false= All)
 *	@param action string Action to authorize member [memory] optional default 'add'
 *	@param astate string State to authorize member [memory] optional default true (false= All)
 *	@param iaction string Action to authorize inherit [memory] optional default 'add'
 *	@param aistate string State to authorize inherit [memory] optional default true (false= All)
 *
 *	@param construct array Construction Workflow [memory] optional default false
 *	@param cparam array Construction Parameters [memory] optional default array()
 *
 *	@param relation string Relation name [memory]
 *	@param owner long int Owner ID [memory] optional default keyid
 *	@param sqlcnd string SQL condition [memory]
 *	@param args array Query parameters [args]
 *	@param escparam array Escape parameters [memory] optional default array()
 *	@param successmsg string Success message [memory] optional default 'Entity added successfully'
 *
 *	@param cache boolean Is cacheable [memory] optional default true
 *	@param expiry int Cache expiry [memory] optional default 150
 *	@param authinh integer Check inherit [memory] optional default 1
 *	@param autherror string Error msg [memory] optional default 'Unable to Authorize'
 *
 *	@param cname string Child name [memory] optional default ''
 *	@param pname string Parent name [memory] optional default ''
 *	@param verb string Activity verb [memory] optional default 'added'
 *	@param join string Activity join [memory] optional default 'to'
 *	@param public integer Public log [memory] optional default 0
 *
 *	@param conn array DataService instance configuration key [memory]
 *
 *	@return id long int Entity ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class EntityAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('conn', 'keyid', 'parent', 'relation', 'sqlcnd'),
			'optional' => array(
				'user' => 'unknown@entity.add',
				'level' => false, 
				'grlevel' => 0,
				'grroot' => 0,
				'owner' => false, 
				'root' => false, 
				'type' => 'general', 
				'path' => '/', 
				'leaf' => false, 
				'authorize' => false, 
				'inherit' => 1, 
				'control' => false, 
				'state' => 'A', 
				'icontrol' => false, 
				'istate' => 'A',
				'acstate' => true,
				'action' => 'add', 
				'astate' => true, 
				'iaction' => 'add', 
				'aistate' => true,
				'escparam' => array(), 
				'successmsg' => 'Entity added successfully', 
				'construct' => false,
				'cparam' => array(),
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
		$memory['msg'] = $memory['successmsg'];
		$memory['owner'] = $memory['owner'] ? $memory['owner'] : $memory['keyid'];
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.add.workflow'
		));
		
		if($memory['construct']){
			foreach($memory['construct'] as $construct)
				array_push($workflow, $construct);
		}
		
		array_push($workflow,
		array(
			'service' => 'transpera.relation.insert.workflow',
			'args' => array_merge($memory['args'], array('id', 'owner', 'user'), $memory['cparam']),
			'escparam' => array_merge($memory['escparam'], array('user')),
			'output' => array('id' => 'entityid')
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