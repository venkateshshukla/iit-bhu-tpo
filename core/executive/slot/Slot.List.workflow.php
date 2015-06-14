<?php 
require_once(SBSERVICE);

/**
 *	@class SlotListWorkflow
 *	@desc Returns all slotes information in batch
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param batchid/id long int Batch ID [memory] optional default STUDENT_PORTAL_ID
 *	@param btname/name string Batch name [memory] optional default ''
 *
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 0
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return slotes array Slots information [memory]
 *	@return batchid long int Batch ID [memory]
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
class SlotListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'batchid' => false, 'id' => STUDENT_PORTAL_ID, 'btname' => false, 'name' => 'IT BHU', 'pgsz' => false, 'pgno' => 0, 'total' => false, 'padmin' => true),
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
			'input' => array('id' => 'batchid', 'pname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`slots`',
			'type' => 'slot',
			'sqlcnd' => "where `slotid` in \${list} order by `rollno`",
			'successmsg' => 'Slots information given successfully',
			//'lsttrack' => true,
			'action' => 'add',
			'output' => array('entities' => 'slots'),
			'mapkey' => 'slotid',
			'mapname' => 'slot',
			'saction' => 'add',
			'authcustom' => array(
				array(
					'service' => 'transpera.relation.unique.workflow',
					'args' => array('keyid'),
					'conn' => 'exconn',
					'relation' => '`students`',
					'sqlprj' => '`stdid`',
					'sqlcnd' => "where `owner`=\${keyid} and `ustatus`<>'0'",
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
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('slots', 'id', 'batchid', 'btname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz');
	}
	
}

?>