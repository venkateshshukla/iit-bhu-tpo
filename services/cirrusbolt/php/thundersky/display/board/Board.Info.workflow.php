<?php 
require_once(SBSERVICE);

/**
 *	@class BoardInfoWorkflow
 *	@desc Returns board information by ID
 *
 *	@param boardid/id long int Board ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param forumid long int Forum ID [memory] optional default 0
 *	@param bname/name string Board name [memory] optional default ''
 *
 *	@param pgsz long int Paging Size [memory] optional default 50
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@return board array Board information [memory]
 *	@return fname string Forum name [memory]
 *	@return forumid long int Forum ID [memory]
 *	@return admin integer Is admin [memory]
 *	@return postadmin integer Is post admin [memory]
 *	@return pgsz long int Paging Size [memory]
 *	@return pgno long int Paging Index [memory] 
 *	@return total long int Paging Total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class BoardInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user'),
			'optional' => array('bname' => false, 'name' => '', 'id' => 0, 'boardid' => false, 'pgsz' => 10, 'pgno' => 0, 'total' => false),
			'set' => array('id', 'name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['boardid'] = $memory['boardid'] ? $memory['boardid'] : $memory['id'];
		$memory['bname'] = $memory['bname'] ? $memory['bname'] : $memory['name'];
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'boardid', 'cname' => 'name'),
			'conn' => 'cbdconn',
			'relation' => '`boards`',
			'type' => 'board',
			'sqlcnd' => "where `boardid`=\${id}",
			'errormsg' => 'Invalid Board ID',
			'successmsg' => 'Board information given successfully',
			'output' => array('entity' => 'board')
		),
		array(
			'service' => 'display.post.list.workflow',
			'output' => array('admin' => 'postadmin'),
			'padmin' => false
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('board', 'bname', 'boardid', 'chain', 'admin', 'posts', 'postadmin', 'total', 'pgsz', 'pgno');
	}
	
}

?>