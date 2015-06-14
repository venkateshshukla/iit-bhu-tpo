<?php 
require_once(SBSERVICE);

/**
 *	@class SelectionEditWorkflow
 *	@desc Edits selection using ID
 *
 *	@param selid long int Selection ID [memory]
 *	@param stage string Stage Name [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param shlstid long int Shortlist ID [memory] optional default 0
 *	@param shlstname string Shortlist Name [memory] optional default ''
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
class SelectionEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'selid', 'stage', 'shlstid', 'shlstname')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['public'] = 1;
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('stage'),
			'input' => array('id' => 'selid', 'cname' => 'name', 'parent' => 'shlstid', 'shlstname' => 'shlstname'),
			'conn' => 'exconn',
			'relation' => '`selections`',
			'type' => 'selection',
			'sqlcnd' => "set `stage`='\${stage}' where `selid`=\${id}",
			'escparam' => array('stage'),
			'init' => false,
			'self' => false,
			'check' => false,
			'successmsg' => 'Selection edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'selid', 'parent' => 'shlstid', 'cname' => 'name', 'shlstname' => 'shlstname'),
			'conn' => 'exconn',
			'relation' => '`selections` l, `students` s, `grades` g',
			'sqlprj' => 'l.`selid`, l.`visitid`, l.`resume`, l.`stage`, l.`name` as `lname`, l.`batch`, s.`stdid`, s.`username`, s.`name`, s.`email`, s.`rollno`, g.`cgpa`, g.`sscx`, g.`hscxii`',
			'sqlcnd' => "where `selid`=\${id} and l.`owner`=s.`owner` and s.`grade`=g.`gradeid`",
			'errormsg' => 'Invalid Selection ID',
			'type' => 'selection',
			'successmsg' => 'Selection information given successfully',
			'output' => array('entity' => 'selection'),
			'auth' => false,
			'track' => false,
			'sinit' => false,
			'cache' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'shlstid'),
			'output' => array('chain' => 'pchain')
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
			
		$memory['padmin'] = $memory['admin'];
		$memory['admin'] = 1;
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('selid', 'shlstid', 'shlstname', 'selection', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>