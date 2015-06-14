<?php 
require_once(SBSERVICE);

/**
 *	@class UpdateEditWorkflow
 *	@desc Edits update using ID
 *
 *	@param updtid long int Update ID [memory]
 *	@param title string Update Title [memory]
 *	@param content string Content [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param boardid long int Board ID [memory] optional default 0
 *	@param bname string Board Name [memory] optional default ''
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
class UpdateEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'updtid', 'title', 'content', 'boardid', 'bname')
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
			'args' => array('title', 'content'),
			'input' => array('id' => 'updtid', 'cname' => 'title', 'parent' => 'boardid', 'pname' => 'bname'),
			'conn' => 'cbdconn',
			'relation' => '`updates`',
			'type' => 'update',
			'sqlcnd' => "set `title`='\${title}', `content`='\${content}' where `updtid`=\${id}",
			'escparam' => array('title', 'content'),
			'check' => false,
			'successmsg' => 'Update edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'updtid', 'parent' => 'boardid', 'cname' => 'name', 'pname' => 'bname'),
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