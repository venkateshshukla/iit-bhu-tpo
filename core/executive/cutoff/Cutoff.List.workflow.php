<?php 
require_once(SBSERVICE);

/**
 *	@class CutoffListWorkflow
 *	@desc Returns all cutoffs information in post
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param visitid/id long int Visit ID [memory] optional default 0
 *	@param vstname/name string Visit name [memory] optional default ''
 *
 *	@param pgsz long int Paging Size [memory] optional default 50
 *	@param pgno long int Paging Index [memory] optional default 0
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return cutoffs array Cutoffs information [memory]
 *	@return visitid long int Visit ID [memory]
 *	@return vstname string Visit Name [memory]
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
class CutoffListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'visitid' => false, 'id' => 0, 'vstname' => false, 'name' => '', 'pgsz' => 15, 'pgno' => 0, 'total' => false, 'padmin' => true),
			'set' => array('id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['visitid'] = $memory['visitid'] ? $memory['visitid'] : $memory['id'];
		$memory['vstname'] = $memory['vstname'] ? $memory['vstname'] : $memory['name'];
		
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'visitid', 'vstname' => 'vstname'),
			'conn' => 'exconn',
			'relation' => '`cutoffs`',
			'type' => 'cutoff',
			'sqlprj' => '`ctfid`,  `dept`, `course`, `eligibility`, `margin`, `max`',
			'sqlcnd' => "where `ctfid` in \${list}",
			'successmsg' => 'Cutoffs information given successfully',
			//'lsttrack' => true,
			'output' => array('entities' => 'cutoffs'),
			'mapkey' => 'ctfid',
			'mapname' => 'cutoff',
			'saction' => 'add'
		);
		
		return  Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('cutoffs', 'visitid', 'vstname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz');
	}
	
}

?>