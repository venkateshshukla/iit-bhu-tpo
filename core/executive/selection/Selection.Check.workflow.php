<?php 
require_once(SBSERVICE);

/**
 *	@class SelectioCheckWorkflow
 *	@desc Checks exists selection
 *
 *	@param name string Name [memory]
 *	@param visitid long int Visit ID [memory]
 *	@param btname long int Batch name [memory]
 *	@param username string Owner Username [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param shlstid long int Shortlist ID [memory] optional default 0
 *	@param shlstname string Shortlist Name [memory] optional default ''
 *	@param level integer Web level [memory] optional default false (inherit post admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
 *
 *	@return selid long int Selection ID [memory]
 *	@return shlstid long int Shortlist ID [memory]
 *	@return shlstname string Shortlist Name [memory]
 *	@return selection array Selection information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SelectionCheckWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'username', 'name', 'visitid', 'btname'),
			'optional' => array('shlstid' => 0, 'shlstname' => '', 'level' => false, 'owner' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['verb'] = 'added';
		$memory['join'] = 'on';
		$memory['public'] = 1;
		
		$workflow = array(
		array(
			'service' => 'guard.key.identify.workflow',
			'input' => array('user' => 'username'),
			'keyid' => false,
			'context' => CONTEXT,
			'output' => array('keyid' => 'owner')
		),
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('owner', 'visitid'),
			'conn' => 'exconn',
			'relation' => '`selections`',
			'sqlprj' => '`selid`',
			'sqlcnd' => "where `owner`=\${owner} and `visitid`=\${visitid}",
			'not' => false,
			'errormsg' => 'User already selected'
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