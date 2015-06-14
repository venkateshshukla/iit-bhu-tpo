<?php 
require_once(SBSERVICE);

/**
 *	@class EntityEditWorkflow
 *	@desc Edits entity using ID
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param id long int Reference ID [memory]
 *	@param acstate string State to authorize chain [memory] optional default true (false= All)
 *	@param action string Action to authorize [memory] optional default 'edit'
 *	@param astate string State to authorize member [memory] optional default true (false= All)
 *	@param iaction string Action to authorize inherit [memory] optional default 'edit'
 *	@param aistate string State to authorize inherit [memory] optional default true (false= All)
 *	@param init boolean init flag [memory] optional default true
 *
 *	@param user string Username [memory] optional default 'unknown@entity.edit'
 *	@param relation string Relation name [memory]
 *	@param sqlcnd string SQL condition [memory]
 *	@param args array Query parameters [args]
 *	@param escparam array Escape parameters [memory] optional default array()
 *	@param successmsg string Success message [memory] optional default 'Entity edited successfully'
 *
 *	@param cache boolean Is cacheable [memory] optional default true
 *	@param expiry int Cache expiry [memory] optional default 150
 *	@param authinh integer Check inherit [memory] optional default 1
 *	@param autherror string Error msg [memory] optional default 'Unable to Authorize'
 *
 *	@param cname string Child name [memory] optional default ''
 *	@param pname string Parent name [memory] optional default ''
 *	@param verb string Activity verb [memory] optional default 'edited'
 *	@param join string Activity join [memory] optional default 'in'
 *	@param public integer Public log [memory] optional default 0
 *
 *	@param conn array DataService instance configuration key [memory]
 *
 *	@return sqlrc integer SQL Row Count [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class EntityEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('conn', 'keyid', 'id', 'relation', 'sqlcnd'),
			'optional' => array(
				'user' => 'unknown@entity.edit',
				'acstate' => true,
				'action' => 'edit', 
				'astate' => true, 
				'iaction' => 'edit', 
				'aistate' => true, 
				'init' => true,
				'escparam' => array(), 
				'successmsg' => 'Entity edited successfully',
				'authinh' => 1,
				'autherror' => 'Unable to Authorize',
				'cname' => '',
				'pname' => '',
				'verb' => 'edited',
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
		$memory['msg'] = $memory['successmsg'];
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow'
		),
		array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array_merge($memory['args'], array('id'))
		),
		array(
			'service' => 'gauge.track.write.workflow'
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
		return array('sqlrc');
	}
	
}

?>