<?php 
require_once(SBSERVICE);

/**
 *	@class WillingnessVisitWorkflow
 *	@desc Returns all willinglists information
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param batchid/id long int Batch/Visit ID [memory] optional default 0
 *	@param btname/name string Batch name [memory] optional default ''
 *
 *	@param pgsz long int Paging Size [memory] optional default 50
 *	@param pgno long int Paging Index [memory] optional default 0
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return willinglists array Willinglists information [memory]
 *	@return batchid long int Batch/Visit ID [memory]
 *	@return btname string Batch Name [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return pgsz long int Paging Size [memory]
 *	@return pgno long int Paging Index [memory]
 *	@return total long int Paging Total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class WillingnessVisitWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'batchid' => false, 'id' => 0, 'btname' => false, 'name' => '', 'pgsz' => false, 'pgno' => 0, 'total' => false, 'padmin' => true),
			'set' => array('id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['batchid'] = $memory['batchid'] ? $memory['batchid'] : $memory['id'];
		$memory['btname'] = $memory['btname'] ? $memory['btname'] : $memory['name'];
		
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'args' => array('batchid'),
			'input' => array('id' => 'batchid', 'btname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`willingnesses`',
			'type' => 'willingness',
			'sqlprj' => 'distinct `visitid`, `name`',
			'sqlcnd' => "where `batchid`=\${batchid} order by visitid desc",
			'successmsg' => 'Willinglists information given successfully',
			'lsttrack' => true,
			'output' => array('entities' => 'willinglists'),
			'action' => 'add',
			'mapkey' => 'visitid',
			'mapname' => 'willinglist',
			'saction' => 'add',
			'authcustom' => array(
				array(
					'service' => 'transpera.relation.unique.workflow',
					'args' => array('keyid'),
					'conn' => 'exconn',
					'relation' => '`students`',
					'sqlprj' => '`stdid`',
					'sqlcnd' => "where `owner`=\${keyid}",
					'errormsg' => 'Unable to Authorize',
					'successmsg' => 'Student information given successfully'
				),
				array(
					'service' => 'cbcore.data.select.service',
					'args' => array('result'),
					'params' => array('result.0.stdid' => 'stdid')
				),
				array(
					'service' => 'transpera.relation.unique.workflow',
					'args' => array('stdid'),
					'conn' => 'cbconn',
					'relation' => '`chains`',
					'sqlprj' => '`parent`',
					'sqlcnd' => "where `type`='person' and `chainid`=\${stdid}",
					'errormsg' => 'Unable to Authorize',
					'successmsg' => 'Parent information given successfully'
				),
				array(
					'service' => 'cbcore.data.select.service',
					'args' => array('result'),
					'params' => array('result.0.parent' => 'parent')
				),
				array(
					'service' => 'transpera.relation.unique.workflow',
					'args' => array('chainid', 'parent'),
					'conn' => 'exconn',
					'relation' => '`batches`',
					'sqlcnd' => "where `batchid`=\${chainid} and `dept`=(select `dept` from `batches` where `batchid`=\${parent})",
					'errormsg' => 'Unable to Authorize',
					'successmsg' => 'Batch information given successfully'
				),
			)
		);
		
		$memory = Snowblozm::run($service, $memory);
		if(!$memory['valid'])
			return $memory;
			
		$memory['uiadmin'] = ($memory['admin'] || $memory['padmin']) && ($memory['btname'] != '');
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('willinglists', 'batchid', 'btname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz', 'uiadmin');
	}
	
}

?>