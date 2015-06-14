<?php 
require_once(SBSERVICE);

/**
 *	@class PostEditWorkflow
 *	@desc Edits post using ID
 *
 *	@param postid long int Post ID [memory]
 *	@param title string Post title [memory]
 *	@param post string Post [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param boardid long int Board ID [memory] optional default 0
 *	@param bname string Board Name [memory] optional default ''
 *
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
class PostEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'postid', 'title', 'post', 'boardid', 'bname')
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
			'args' => array('title', 'post'),
			'input' => array('id' => 'postid', 'cname' => 'title'),
			'conn' => 'cbdconn',
			'relation' => '`posts`',
			'type' => 'post',
			'sqlcnd' => "set `title`='\${title}', `post`='\${post}' where `postid`=\${id}",
			'escparam' => array('title', 'post'),
			'check' => false,
			'successmsg' => 'Post edited successfully'
		),
		array(
			'service' => 'display.post.info.workflow',
			'pgsz' => 5,
			'padmin' => true
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('postid', 'boardid', 'bname', 'post',  'comments', 'chain', 'pchain', 'admin', 'padmin', 'cmntadmin', 'total', 'pgsz', 'pgno');
	}
	
}

?>