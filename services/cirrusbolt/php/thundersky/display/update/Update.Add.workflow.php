<?php 
require_once(SBSERVICE);

/**
 *	@class UpdateAddWorkflow
 *	@desc Adds new update
 *
 *	@param title string Update Title [memory]
 *	@param content string Content [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param boardid long int Board ID [memory] optional default 0
 *	@param bname string Board Name [memory] optional default ''
 *	@param level integer Web level [memory] optional default false (inherit quiz admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
 *
 *	@param mailto string Mail To [memory] optional default 'update_mailto' Snowblozm [boardid]
 *
 *	@return updtid long int Update ID [memory]
 *	@return boardid long int Board ID [memory]
 *	@return bname string Board Name [memory]
 *	@return update array Update information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class UpdateAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'title', 'content'),
			'optional' => array('boardid' => 0, 'bname' => '', 'level' => false, 'owner' => false, 'mailto' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['verb'] = 'added';
		$memory['join'] = 'on';
		$memory['public'] = 1;
		
		if(!$memory['mailto']){
			$mailto = Snowblozm::get('update_mailto');
			$memory['mailto'] = $mailto[$memory['boardid']];
		}
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.add.workflow',
			'args' => array('title', 'content'),
			'input' => array('parent' => 'boardid', 'cname' => 'title', 'pname' => 'bname'),
			'conn' => 'cbdconn',
			'relation' => '`updates`',
			'type' => 'update',
			'sqlcnd' => "(`updtid`, `owner`, `title`, `content`) values (\${id}, \${owner}, '\${title}', '\${content}')",
			'escparam' => array('title', 'content'),
			'successmsg' => 'Update added successfully',
			'output' => array('id' => 'updtid')
		),
		array(
			'service' => 'queue.mail.add.workflow',
			'input' => array('queid' => 'updtid', 'to' => 'mailto', 'body' => 'content'),
			'subject' => '['.$memory['bname'].'] '.$memory['title']
		), 
		array(
			'service' => 'queue.mail.send.workflow',
			'input' => array('queid' => 'updtid')
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'updtid', 'parent' => 'boardid', 'cname' => 'name', 'bname' => 'bname'),
			'conn' => 'cbdconn',
			'relation' => '`updates`',
			'sqlcnd' => "where `updtid`=\${id}",
			'errormsg' => 'Invalid Update ID',
			'type' => 'update',
			'successmsg' => 'Update information given successfully',
			'output' => array('entity' => 'update'),
			'auth' => false,
			'track' => false,
			'sinit' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'boardid'),
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
		return array('updtid', 'boardid', 'bname', 'update', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>