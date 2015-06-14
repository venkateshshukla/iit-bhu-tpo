<?php 
require_once(SBSERVICE);

/**
 *	@class CommentEditWorkflow
 *	@desc Edits comment using ID
 *
 *	@param cmtid long int Comment ID [memory]
 *	@param comment string Comment [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param postid long int Post ID [memory] optional default 0
 *	@param pname string Post Name [memory] optional default ''
 *
 *	@return cmtid long int Comment ID [memory]
 *	@return postid long int Post ID [memory]
 *	@return pname string Post Name [memory]
 *	@return comment array Comment information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class CommentEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'cmtid', 'comment', 'postid', 'pname')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){		
		$memory['public'] = 1;
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('comment'),
			'input' => array('id' => 'cmtid', 'cname' => 'comment', 'parent' => 'postid', 'pname' => 'pname'),
			'conn' => 'cbdconn',
			'relation' => '`comments`',
			'type' => 'comment',
			'sqlcnd' => "set `comment`='\${comment}' where `cmtid`=\${id}",
			'escparam' => array('comment'),
			'check' => false,
			'successmsg' => 'Comment edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'cmtid', 'parent' => 'postid', 'cname' => 'name', 'pname' => 'pname'),
			'conn' => 'cbdconn',
			'relation' => '`comments`',
			'sqlcnd' => "where `cmtid`=\${id}",
			'errormsg' => 'Invalid Comment ID',
			'type' => 'comment',
			'successmsg' => 'Comment information given successfully',
			'output' => array('entity' => 'comment'),
			'auth' => false,
			'track' => false,
			'sinit' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'postid'),
			'output' => array('chain' => 'pchain')
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
			
		$memory['padmin'] = $memory['admin'];
		$memory['admin'] = 1;
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('cmtid', 'postid', 'pname', 'comment', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>