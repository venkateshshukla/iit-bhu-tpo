<?php 
require_once(SBSERVICE);

/**
 *	@class UpdateListWorkflow
 *	@desc Returns all updates information in quiz
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param boardid/id long int Board ID [memory] optional default 0
 *	@param bname/name string Board name [memory] optional default ''
 *
 *	@param pgsz long int Paging Size [memory] optional default 25
 *	@param pgno long int Paging Index [memory] optional default 0
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return updates array Updates information [memory]
 *	@return boardid long int Board ID [memory]
 *	@return bname string Board Name [memory]
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
class UpdateListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'boardid' => false, 'id' => 0, 'bname' => false, 'name' => '', 'pgsz' => 15, 'pgno' => 0, 'total' => false, 'padmin' => true),
			'set' => array('id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['boardid'] = $memory['boardid'] ? $memory['boardid'] : $memory['id'];
		$memory['bname'] = $memory['bname'] ? $memory['bname'] : $memory['name'];
		
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'boardid', 'pname' => 'bname'),
			'conn' => 'cbdconn',
			'relation' => '`updates`',
			'type' => 'update',
			'sqlprj' => '`updtid`, `title`, `content`',
			'sqlcnd' => "where `updtid` in \${list} order by `updtid` desc",
			'successmsg' => 'Updates information given successfully',
			//'lsttrack' => true,
			'output' => array('entities' => 'updates'),
			'mapkey' => 'updtid',
			'mapname' => 'update',
			'saction' => 'add',
			'siaction' => 'add',
			'iaction' => 'add'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('updates', 'boardid', 'bname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz');
	}
	
}

?>