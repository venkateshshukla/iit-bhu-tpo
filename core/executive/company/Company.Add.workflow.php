<?php 
require_once(SBSERVICE);

/**
 *	@class CompanyAddWorkflow
 *	@desc Adds new company
 *
 *	@param name string Company name [memory]
 *	@param username string Company username [memory]
 *	@param email string Email [memory]
 *
 *	@param keyid long int Usage Key [memory]
 *	@param portalid long int Batch ID [memory] optional default COMPANY_PORTAL_ID
 *	@param plname string Batch Name [memory] optional default ''
 *	@param level integer Web level [memory] optional default 1 (batch admin access allowed)
 *
 *	@return comid long int Company ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class CompanyAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'name'),
			'optional' => array('portalid' => COMPANY_PORTAL_ID, 'level' => 2, 'plname' => '', 'username' => '', 'email' => ''),
			'set' => array('portalid', 'plname')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Company created successfully. Registration verification pending.';
		$memory['level'] = 2;
		$memory['name'] = $memory['name'];
		$name = implode('-', preg_split('/[\W]+/', strtolower($memory['name'])));
		$memory[ 'username' ] = $name;
		$memory[ 'email' ] = $name.'.tpo@iitbhu.ac.in';
		
		$memory['verb'] = 'registered';
		$memory['join'] = 'on';
		$memory['public'] = 1;
		
		$workflow = array(
		array(
			'service' => 'people.person.add.workflow',
			'input' => array('peopleid' => 'portalid', 'cname' => 'username'),
			'country' => 'India',
			'human' => false,
			'recaptcha_challenge_field' => false, 
			'recaptcha_response_field' => false,
			'device' => 'mail'
		),
		array(
			'service' => 'display.board.add.workflow',
			'input' => array('forumid' => 'portalid', 'fname' => 'plname', 'user' => 'username'),
			'bname' => $memory['name']."'s Notes",
			'desc' => 'Links, Discussions and Interview Materials',
			'level' => 2,
			'output' => array('boardid' => 'notes')
		),
		array(
			'service' => 'storage.directory.add.workflow',
			'name' => 'storage/private/folders/'.$memory['username'].'/',
			'path' => 'storage/private/folders/'.$memory['username'].'/',
			'input' => array('stgid' => 'portalid', 'user' => 'username'),
			'authorize' => 'add:remove:edit:grlist',
			'grlevel' => -2,
			'grroot' => STUDENT_PORTAL_ID,
			'output' => array('dirid' => 'folder')
		),
		array(
			'service' => 'transpera.relation.insert.workflow',
			'args' => array('pnid', 'owner', 'username', 'name', 'folder', 'notes'),
			'conn' => 'exconn',
			'relation' => '`companies`',
			'sqlcnd' => "(`comid`,`owner`, `username`, `name`,  `folder`, `notes`) values (\${pnid}, \${owner}, '\${username}', '\${name}', \${folder}, \${notes})",
			'escparam' => array('username', 'name', 'email'),
			'output' => array('id' => 'comid')
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
		
		/*$memory = Snowblozm::run(array(
			'service' => 'people.person.send.workflow'
		), $memory);
		
		if($memory['valid'])*/
			$memory['msg'] = 'Company account created successfully. Verification sent successfully.';
		/*else
			$memory['msg'] = 'Company account created successfully. Error sending verification mail.';*/
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'pnid', 'parent' => 'portalid', 'cname' => 'name', 'plname' => 'plname'),
			'conn' => 'exconn',
			'relation' => '`companies`',
			'sqlcnd' => "where `comid`=\${id}",
			'errormsg' => 'Invalid Company ID',
			'type' => 'person',
			'successmsg' => 'Batch information given successfully',
			'output' => array('entity' => 'company'),
			'auth' => false,
			'track' => false,
			'sinit' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'portalid'),
			'output' => array('chain' => 'pchain')
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		$memory['padmin'] = $memory['admin'];
		$memory['admin'] = 1;
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('comid',  'portalid', 'plname', 'company', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>