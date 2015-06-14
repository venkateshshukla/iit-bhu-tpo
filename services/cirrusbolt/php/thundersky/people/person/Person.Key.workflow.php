<?php 
require_once(SBSERVICE);

/**
 *	@class PersonKeyWorkflow
 *	@desc Changes key for person by ID
 *
 *	@param pnid long int Person ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string User name [memory]
 *	@param keyvalue string Key value [memory]
 *	@param currentuser string Current Username
 *	@param currentkey string Current key value [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PersonKeyWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'keyvalue', 'currentkey', 'currentuser', 'pnid')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Password changed successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('pnid'),
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'sqlcnd' => "where `pnid`=\${pnid}",
			'errormsg' => 'Invalid Person ID'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0.owner' => 'owner', 'result.0.pnid' => 'pnid')
		),
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'pnid'),
			'action' => 'edit'
		),
		array(
			'service' => 'guard.key.authenticate.workflow',
			'input' => array('user' => 'currentuser', 'key' => 'currentkey')
		),
		array(
			'service' => 'transpera.reference.master.workflow',
			'input' => array('id' => 'pnid', 'cname' => 'user')
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