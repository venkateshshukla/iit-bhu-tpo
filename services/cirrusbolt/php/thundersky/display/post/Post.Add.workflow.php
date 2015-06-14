<?php 
require_once(SBSERVICE);

/**
 *	@class PostAddWorkflow
 *	@desc Adds new post
 *
 *	@param title string Post title [memory]
 *	@param post string Post [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param boardid long int Board ID [memory] optional default 0
 *	@param bname string Board Name [memory] optional default ''
 *	@param level integer Web level [memory] optional default false (inherit board admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
 *	@param view-access char View Access [memory] optional default false ('A', 'L', 'P', false=inherit)
 *	@param cmnt-access char Comment Access [memory] optional default false ('A', 'L', 'P', false=inherit)
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
class PostAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'title', 'post'),
			'optional' => array('boardid' => 0, 'bname' => '', 'level' => false, 'owner' => false, 'view-access' => false, 'cmnt-access' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['verb'] = 'posted';
		$memory['join'] = 'to';
		$memory['public'] = 1;
		
		$auth = 'edit:remove:';
		
		switch($memory['view-access']){
			case 'P' :
				$auth .= 'pbinfo:pblist';
				break;
			case 'L' :
				$auth .= '';
				break;
			case 'A' :
				$auth .= 'info:list:';
				break;
			default :
				$auth = false;
		}
		
		switch($memory['cmnt-access']){
			case 'P' :
				$auth .= 'pbadd';
				break;
			case 'L' :
				$auth .= '';
				break;
			case 'A' :
				$auth .= 'add:';
				break;
			default :
				$auth = false;
		}
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.add.workflow',
			'args' => array('title', 'post'),
			'input' => array('parent' => 'boardid', 'cname' => 'title', 'pname' => 'bname'),
			'conn' => 'cbdconn',
			'relation' => '`posts`',
			'type' => 'post',
			'authorize' => $auth,
			'sqlcnd' => "(`postid`, `owner`, `title`, `post`) values (\${id}, \${owner}, '\${title}', '\${post}')",
			'escparam' => array('title', 'post'),
			'successmsg' => 'Post added successfully',
			'output' => array('id' => 'postid')
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
		return array('postid', 'boardid', 'bname', 'post', 'comments', 'chain', 'pchain', 'admin', 'padmin', 'cmntadmin', 'total', 'pgsz', 'pgno');
	}
	
}

?>