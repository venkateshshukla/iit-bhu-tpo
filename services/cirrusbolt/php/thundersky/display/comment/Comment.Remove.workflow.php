<?php 
require_once(SBSERVICE);

/**
 *	@class CommentRemoveWorkflow
 *	@desc Removes comment by ID
 *
 *	@param cmtid long int Comment ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param postid long int Post ID [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class CommentRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'cmtid'),
			'optional' => array('postid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.remove.workflow',
			'input' => array('id' => 'cmtid', 'parent' => 'postid'),
			'conn' => 'cbdconn',
			'relation' => '`comments`',
			'type' => 'comment',
			'sqlcnd' => "where `cmtid`=\${id}",
			'errormsg' => 'Invalid Comment ID',
			'successmsg' => 'Comment removed successfully'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>