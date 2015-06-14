<?php 
require_once(SBSERVICE);

/**
 *	@class CommentInfoWorkflow
 *	@desc Returns comment information by ID
 *
 *	@param cmtid/id long int Comment ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param user string Key User [memory]
 *	@param postid long int Post ID [memory] optional default 0
 *	@param pname/name string Post name [memory] optional default ''
 *
 *	@return comment array Comment information [memory]
 *	@return pname string Post name [memory]
 *	@return postid long int Post ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class CommentInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('cmtid'),
			'optional' => array('keyid' => false, 'user' => '', 'pname' => false, 'name' => '', 'postid' => false, 'id' => 0),
			'set' => array('id', 'name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['cmtid'] = $memory['cmtid'] ? $memory['cmtid'] : $memory['id'];
		
		$service = array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'cmtid', 'parent' => 'postid', 'cname' => 'name', 'pname' => 'pname'),
			'conn' => 'cbdconn',
			'relation' => '`comments`',
			'sqlcnd' => "where `cmtid`=\${id}",
			'errormsg' => 'Invalid Comment ID',
			'type' => 'comment',
			'successmsg' => 'Comment information given successfully',
			'output' => array('entity' => 'comment')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('comment', 'pname', 'postid', 'admin');
	}
	
}

?>