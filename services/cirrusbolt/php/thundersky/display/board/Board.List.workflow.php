<?php 
require_once(SBSERVICE);

/**
 *	@class BoardListWorkflow
 *	@desc Returns all boards information in forum
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param forumid/id long int Forum ID [memory] optional default 0
 *	@param fname/name string Forum name [memory] optional default ''
 *
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@return boards array Boards information [memory]
 *	@return forumid long int Forum ID [memory]
 *	@return fname string Forum name [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class BoardListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('forumid' => false, 'id' => 0, 'fname' => false, 'name' => '', 'pgsz' => false, 'pgno' => 0, 'total' => false),
			'set' => array('id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['forumid'] = $memory['forumid'] ? $memory['forumid'] : $memory['id'];
		$memory['fname'] = $memory['fname'] ? $memory['fname'] : $memory['name'];
		
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'forumid', 'pname' => 'fname'),
			'conn' => 'cbdconn',
			'relation' => '`boards`',
			'type' => 'board',
			'sqlcnd' => "where `boardid` in \${list} order by `boardid` desc",
			'successmsg' => 'Boards information given successfully',
			'output' => array('entities' => 'boards'),
			'mapkey' => 'boardid',
			'mapname' => 'board'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('boards', 'forumid', 'fname', 'admin', 'total', 'pgsz');
	}
	
}

?>