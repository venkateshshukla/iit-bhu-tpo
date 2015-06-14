<?php 
require_once(SBSERVICE);

/**
 *	@class CommentListWorkflow
 *	@desc Returns all comments information in post
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param postid/id long int Post ID [memory] optional default 0
 *	@param pname/name string Post name [memory] optional default ''
 *
 *	@param pgsz long int Paging Size [memory] optional default 50
 *	@param pgno long int Paging Index [memory] optional default 0
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return comments array Comments information [memory]
 *	@return postid long int Post ID [memory]
 *	@return pname string Post Name [memory]
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
class CommentListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'postid' => false, 'id' => 0, 'pname' => false, 'name' => '', 'pgsz' => 15, 'pgno' => 0, 'total' => false, 'padmin' => true),
			'set' => array('id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['postid'] = $memory['postid'] ? $memory['postid'] : $memory['id'];
		$memory['pname'] = $memory['pname'] ? $memory['pname'] : $memory['name'];
		
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'postid', 'pname' => 'pname'),
			'conn' => 'cbdconn',
			'relation' => '`comments`',
			'type' => 'comment',
			'sqlprj' => '`cmtid`, `comment`, `reply`',
			'sqlcnd' => "where `cmtid` in \${list} order by `cmtid` desc",
			'successmsg' => 'Comments information given successfully',
			'lsttrack' => true,
			'output' => array('entities' => 'comments'),
			'mapkey' => 'cmtid',
			'mapname' => 'comment',
			'saction' => 'add'
		);
		
		$memory = Snowblozm::run($service, $memory);
		if(!$memory['valid'])
			return $memory;
		
		/*if($memory['status'] == 403 || $memory['status'] == 407){
			$memory['valid'] = true;
			$memory['comments'] = array();
			$memory['admin'] = 0;
			return $memory;
		}*/
		
		$memory['comments'] = array_reverse($memory['comments']);
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('comments', 'postid', 'pname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz');
	}
	
}

?>