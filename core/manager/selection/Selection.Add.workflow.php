<?php 
require_once(SBSERVICE);

/**
 *	@class SelectionAddWorkflow
 *	@desc Adds new selection
 *
 *	@param stageid long int Stage ID [memory]
 *	@param refer long int Refer ID [memory]
 *	@param status integer Status [memory] optional default 1
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param owner long int Owner Key ID [memory] optional default keyid
 *	@param shlstid long int Shortlist ID [memory] optional default 0
 *	@param level integer Web level [memory] optional default false (inherit shortlist admin access)
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
class SelectionAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'username', 'stageid'),
			'optional' => array('shlstid' => 0, 'level' => false, 'status' => 1, 'owner' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$workflow = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('username'),
			'conn' => 'exconn',
			'relation' => '`students`',
			'sqlcnd' => "where `username`='\${username}'",
			'escparam' => array('username'),
			'errormsg' => 'Invalid Student Username',
			'successmsg' => 'Student information given successfully'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0' => 'student', 'result.0.stdid' => 'stdid')
		),
		array(
			'service' => 'shortlist.selection.add.workflow',
			'input' => array('refer' => 'stdid')
		),
		array(
			'service' => 'shortlist.stage.info.workflow',
			'output' => array('admin' => 'stgadmin')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('selid', 'shlstid', 'shlstname', 'selection', 'student', 'stage', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>