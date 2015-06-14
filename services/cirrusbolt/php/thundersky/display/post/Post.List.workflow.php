<?php 
require_once(SBSERVICE);

/**
 *	@class PostListWorkflow
 *	@desc Returns all posts information in board
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param boardid/id long int Board ID [memory] optional default 0
 *	@param bname/name string Board name [memory] optional default ''
 *
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return posts array Posts information [memory]
 *	@return boardid long int Board ID [memory]
 *	@return bname string Board name [memory]
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
class PostListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user'),
			'optional' => array('boardid' => false, 'id' => 0, 'bname' => false, 'name' => '', 'pgsz' => 50, 'pgno' => 0, 'total' => false, 'padmin' => true),
			'set' => array('id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['boardid'] = $memory['boardid'] ? $memory['boardid'] : $memory['id'];
		$memory['bname'] = $memory['bname'] ? $memory['bname'] : $memory['name'];
		$memory['pchain'] = array();
		
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'boardid', 'pname' => 'bname'),
			'conn' => 'cbdconn',
			'relation' => '`posts`',
			'type' => 'post',
			'sqlprj' => '`postid`, `title`,`post`',
			'sqlcnd' => "where `postid` in \${list} order by `postid` desc",
			'successmsg' => 'Posts information given successfully',
			'lsttrack' => true,
			'output' => array('entities' => 'posts'),
			'mapkey' => 'postid',
			'mapname' => 'post',
			'saction' => 'add'
		);
		
		$memory = Snowblozm::run($service, $memory);
		if(!$memory['valid'])
			return $memory;
		
		$len = count($memory['posts']);
		for($i=0; $i<$len; $i++){
			$post = $memory['posts'][$i];
			$service = array(
				'service' => 'display.comment.list.workflow',
				'output' => array('admin' => 'cmntadmin', 'padmin' => 'admin'),
				'keyid' => $memory['keyid'],
				'user' => $memory['user'],
				'postid' => $post['post']['postid'],
				'pname' => $post['post']['title'],
				'pgsz' => 3,
				'pgno' => 0,
				'total' => $post['chain']['count'],
				//'chpgsz' => 3,
				//'chpgno' => 0,
				//'chtotal' => $post['chain']['count']
			);
			$memory['posts'][$i] = Snowblozm::run($service, $post);
		}
		
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('posts', 'boardid', 'bname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz');
	}
	
}

?>