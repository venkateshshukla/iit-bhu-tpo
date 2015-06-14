<?php 
require_once(SBSERVICE);

/**
 *	@class BatchEditWorkflow
 *	@desc Edits batch using ID
 *
 *	@param batchid long int Batch ID [memory]
 *	@param btname string Batch name [memory]
 *	@param dept string Department [memory]
 *	@param course string Course [memory]
 *	@param year integer Year [memory] 
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param portalid long int Portal ID [memory] optional default 0
 *	@param plname string Portal Name [memory] optional default ''
 *
 *	@return batchid long int Batch ID [memory]
 *	@return portalid long int Portal ID [memory]
 *	@return plname string Portal Name [memory]
 *	@return batch array Batch information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class BatchEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'batchid', 'btname', 'dept', 'course', 'year', 'portalid', 'plname')
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
			'args' => array('btname', 'dept', 'course', 'year'),
			'input' => array('id' => 'batchid', 'cname' => 'btname', 'parent' => 'portalid', 'pname' => 'plname'),
			'conn' => 'exconn',
			'relation' => '`batches`',
			'type' => 'batch',
			'sqlcnd' => "set `btname`='\${btname}', `dept`='\${dept}', `course`='\${course}', `year`=\${year} where `batchid`=\${id}",
			'escparam' => array('btname', 'dept', 'course'),
			'check' => false,
			'successmsg' => 'Batch edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'batchid', 'parent' => 'portalid', 'cname' => 'name', 'pname' => 'plname'),
			'conn' => 'exconn',
			'relation' => '`batches`',
			'sqlcnd' => "where `batchid`=\${id}",
			'errormsg' => 'Invalid Batch ID',
			'type' => 'batch',
			'successmsg' => 'Batch information given successfully',
			'output' => array('entity' => 'batch'),
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
		return array('batchid', 'portalid', 'plname', 'batch', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>