<?php 
require_once(SBSERVICE);

/**
 *	@class SelectionAddWorkflow
 *	@desc Adds new selection for shortlist
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
			'required' => array('keyid', 'user', 'refer', 'stageid'),
			'optional' => array('shlstid' => 0, 'level' => false, 'status' => 1, 'owner' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$workflow = array(
		array(
			'service' => 'transpera.entity.add.workflow',
			'input' => array('parent' => 'shlstid'),
			'authorize' => 'edit:add:remove:list:qualify',
			'args' => array('stageid', 'status'),
			'conn' => 'cbslconn',
			'relation' => '`selections`',
			'type' => 'selection',
			'sqlcnd' => "(`selid`, `owner`, `stageid`, `refer`, `status`) values (\${id}, \${owner}, \${stageid}, \${refer}, \${status})"
			'output' => array('id' => 'selid'),
			'successmsg' => 'Selection added successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'selid', 'parent' => 'shlstid', 'cname' => 'name', 'pname' => 'shlstname'),
			'conn' => 'cbslconn',
			'relation' => '`selections`',
			'sqlprj' => '`selid`, `stageid`, `refer`, `status`',
			'sqlcnd' => "where `selid`=\${id}",
			'errormsg' => 'Invalid Selection ID',
			'type' => 'selection',
			'successmsg' => 'Selection information given successfully',
			'output' => array('entity' => 'selection'),
			'auth' => false,
			'track' => false,
			'sinit' => false
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
		
		$service = array(
			'service' => 'transpera.reference.add.workflow',
			
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('selid', 'shlstid', 'shlstname', 'selection', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>