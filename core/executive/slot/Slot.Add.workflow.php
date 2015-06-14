<?php 
require_once(SBSERVICE);

/**
 *	@class SlotAddWorkflow
 *	@desc Adds new slot
 *
 *	@param username string Slot username [memory]
 *	@param rollno string Slot rollno [memory]
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param batchid long int Batch ID [memory] optional default 0
 *	@param btname string Batch Name [memory] optional default ''
 *	@param level integer Web level [memory] optional default false (inherit portal admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
 *
 *	@return slotid long int Slot ID [memory]
 *	@return batchid long int Batch ID [memory]
 *	@return btname string Batch Name [memory]
 *	@return slot array Slot information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SlotAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'username', 'rollno'),
			'optional' => array('batchid' => 0, 'btname' => '', 'level' => false, 'owner' => false)
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
			'service' => 'transpera.entity.add.workflow',
			'args' => array('username', 'rollno'),
			'input' => array('parent' => 'batchid', 'cname' => 'btname', 'pname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`slots`',
			'type' => 'slot',
			'authorize' => 'info:add:edit:remove:list',
			'sqlcnd' => "(`slotid`, `owner`, `username`, `rollno`) values (\${id}, \${owner}, '\${username}', '\${rollno}')",
			'escparam' => array('username', 'rollno'),
			'successmsg' => 'Slot added successfully',
			'output' => array('id' => 'slotid')
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'slotid', 'parent' => 'batchid', 'cname' => 'name', 'btname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`slots`',
			'sqlcnd' => "where `slotid`=\${id}",
			'errormsg' => 'Invalid Slot ID',
			'type' => 'slot',
			'successmsg' => 'Slot information given successfully',
			'output' => array('entity' => 'slot'),
			'auth' => false,
			'track' => false,
			'sinit' => false
		),
		array(
			'service' => 'executive.batch.info.workflow',
			'output' => array('chain' => 'pchain')
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
		return array('slotid', 'batchid', 'btname', 'slot', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>