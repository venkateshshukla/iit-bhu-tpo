<?php 
require_once(SBSERVICE);

/**
 *	@class PostInfoWorkflow
 *	@desc Returns post information by ID
 *
 *	@param postid/id long int Post ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param pname/name string Post title [memory] optional default ''
 *	@param boardid long int Board ID [memory] optional default 0
 *	@param bname string Board Name [memory] optional default ''
 *
 *	@param pgsz long int Paging Size [memory] optional default 50
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return post array Post information [memory]
  *	@return postid long int Post ID [memory]
  *	@return pname string Post name [memory]
 *	@return boardid long int Board ID [memory]
 *	@return bname string Board Name [memory]
 *	@return post array Post information [memory]
 *	@return comments array Comments information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *	@return cmntadmin integer Is comment admin [memory]
 *	@return pgsz long int Paging Size [memory]
 *	@return pgno long int Paging Index [memory] 
 *	@return total long int Paging Total [memory] 
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PostInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user'),
			'optional' => array('pname' => false, 'name' => '', 'id' => 0, 'postid' => false, 'pgsz' => 5, 'pgno' => 0, 'total' => false, 'boardid' => -1, 'bname' => '', 'padmin' => false),
			'set' => array('id', 'name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['postid'] = $memory['postid'] ? $memory['postid'] : $memory['id'];
		$memory['pname'] = $memory['pname'] ? $memory['pname'] : $memory['name'];
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'postid', 'parent' => 'boardid', 'cname' => 'name', 'pname' => 'bname'),
			'conn' => 'cbdconn',
			'relation' => '`posts`',
			'type' => 'post',
			'sqlcnd' => "where `postid`=\${id}",
			'errormsg' => 'Invalid Post ID',
			'successmsg' => 'Post information given successfully',
			'output' => array('entity' => 'post'),
			'auth' => $memory['padmin'] !== true,
			'track' => $memory['padmin'] !== true,
			'sinit' => $memory['padmin'] !== true
		),
		array(
			'service' => 'display.comment.list.workflow',
			'output' => array('admin' => 'cmntadmin', 'padmin' => 'postadmin'),
			'padmin' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'boardid'),
			'output' => array('chain' => 'pchain')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('post', 'pname', 'postid', 'admin', 'boardid', 'bname', 'post', 'comments', 'cmntadmin', 'chain', 'pchain', 'admin', 'padmin', 'total', 'pgsz', 'pgno');
	}
	
}

?>