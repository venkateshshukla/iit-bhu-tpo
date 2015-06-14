<?php 
require_once(SBSERVICE);

/**
 *	@class CommentAddWorkflow
 *	@desc Adds new comment
 *
 *	@param comment string Comment [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param postid long int Post ID [memory] optional default 0
 *	@param pname string Post Name [memory] optional default ''
 *	@param level integer Web level [memory] optional default false (inherit post admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
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
class CommentAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'comment'),
			'optional' => array('postid' => 0, 'pname' => '', 'level' => false, 'owner' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['verb'] = 'commented';
		$memory['join'] = 'on';
		$memory['public'] = 1;
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.add.workflow',
			'args' => array('comment'),
			'input' => array('parent' => 'postid', 'cname' => 'comment', 'pname' => 'pname'),
			'conn' => 'cbdconn',
			'relation' => '`comments`',
			'type' => 'comment',
			'sqlcnd' => "(`cmtid`, `owner`, `comment`) values (\${id}, \${owner}, '\${comment}')",
			'escparam' => array('comment'),
			'successmsg' => 'Comment added successfully',
			'output' => array('id' => 'cmtid')
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
			
		if($memory['user'] != $memory['pchain']['user']){
			$comment = $memory['comment'];
			Snowblozm::run(array(
				'service' => 'people.person.alert.workflow',
				'input' => array('chainid' => 'postid', 'queid' => 'postid'),
				'subject' => FORUM_MAIL_SUBJECT_PREFIX.' User '.$memory['user'].' commented on your post',
				'body' => '<strong>'.$memory['pname'].'</strong>
				<p><em>'.$memory['user'].'</em> : '.$comment['comment'].'</p>
				--<br />'.FORUM_MAIL_BODY_SIGNATURE
			), $memory);
		}
		
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