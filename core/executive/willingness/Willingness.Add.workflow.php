<?php 
require_once(SBSERVICE);

/**
 *	@class WillingnessAddWorkflow
 *	@desc Adds new willingness
 *
 *	@param name string Name [memory]
 *	@param visitid long int Visit ID [memory]
 *	@param btname long int Batch name [memory]
 *	@param username string Owner Username [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param level integer Web level [memory] optional default false (inherit post admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
 *
 *	@return wlgsid long int Willingness ID [memory]
 *	@return batchid long int Batch ID [memory]
 *	@return btname string Batch Name [memory]
 *	@return willingness array Willingness information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class WillingnessAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'username', 'name', 'visitid', 'btname'),
			'optional' => array('batchid' => 0, 'btname' => '', 'level' => 2, 'owner' => false)
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
			'relation' => '`willingnesses`',
			'sqlprj' => '`wlgsid`',
			'sqlcnd' => "where `owner`=\${owner} and `visitid`=\${visitid}",
			'not' => false,
			'errormsg' => 'User already added'
		),
		array(
			'service' => 'executive.batch.find.workflow',
			'output' => array('resumes' => 'resdir')
		),
		array(
			'service' => 'transpera.entity.add.workflow',
			'args' => array('visitid', 'name', 'owner', 'batchid', 'resdir', 'btname'),
			'input' => array('parent' => 'batchid', 'cname' => 'eligibility', 'btname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`willingnesses`',
			'type' => 'willingness',
			'sqlcnd' => "(`wlgsid`, `owner`, `visitid`, `batchid`, `resdir`, `name`, `batch`) values (\${id}, \${owner}, \${visitid}, \${batchid}, \${resdir}, '\${name}', '\${btname}')",
			'escparam' => array('name', 'btname'),
			'successmsg' => 'Willingness added successfully',
			'output' => array('id' => 'wlgsid')
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'wlgsid', 'parent' => 'batchid', 'cname' => 'name', 'btname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`willingnesses` w, `students` s, `grades` g',
			'sqlprj' => 'w.`wlgsid`, w.`visitid`, w.`resume`, w.`status`, w.`approval`, w.`name` as `wname`, w.`batch`, s.`stdid`, s.`username`, s.`name`, s.`email`, s.`rollno`, g.`cgpa`, g.`sscx`, g.`hscxii`',
			'sqlcnd' => "where `wlgsid`=\${id} and w.`owner`=s.`owner` and s.`grade`=g.`gradeid`",
			'errormsg' => 'Invalid Willingness ID',
			'type' => 'willingness',
			'successmsg' => 'Willingness information given successfully',
			'output' => array('entity' => 'willingness'),
			'auth' => false,
			'track' => false,
			'sinit' => false,
			'cache' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'batchid'),
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
		return array('wlgsid', 'batchid', 'btname', 'willingness', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>