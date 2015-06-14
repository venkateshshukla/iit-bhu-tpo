<?php 
require_once(SBSERVICE);

/**
 *	@class OpenidListWorkflow
 *	@desc Returns openid key IDs in chain
 *
 *	@param keyid long int Key ID [memory]
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@return openids array Openid emails information [memory]
 *	@return total long int Paging total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class OpenidListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('pgsz' => false, 'pgno' => 0, 'total' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Openid emails returned successfully';
		
		$service = array(
			'service' => 'transpera.relation.select.workflow',
			'args' => array('keyid'),
			'conn' => 'cbconn',
			'relation' => '`openids`',
			'sqlprj' => '`oid`, `email`',
			'sqlcnd' => "where `keyid`=\${keyid}",
			'check' => false,
			'errormsg' => 'No Openids',
			'mapkey' => 'oid',
			'mapname' => 'identity',
			'output' => array('result' => 'openids')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('openids', 'total');
	}
	
}

?>