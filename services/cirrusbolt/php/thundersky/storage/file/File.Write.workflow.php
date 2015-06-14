<?php 
require_once(SBSERVICE);

/**
 *	@class FileWriteWorkflow
 *	@desc Uploads file and its file information
 *
 *	@param fileid long int File ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param filekey string File key [memory] optional default 'storage'
 *	@param mime string MIME type [memory] optional default 'application/force-download'
 *	@param maxsize long int Maximum size [memory] optional default false
 *	@param dirid long int Directory ID [memory] optional default 0
 *
 *	@return filename string Filename received [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class FileWriteWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'fileid'),
			'optional' => array('dirid' => 0, 'dirname' => '', 'maxsize' => false, 'mime' => 'application/force-download', 'filekey' => 'storage')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'File written successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.authorize.workflow',
			'input' => array('id' => 'fileid'),
			'action' => 'edit'
		),
		array(
			'service' => 'storage.directory.info.workflow'
		),
		array(
			'service' => 'storage.file.info.workflow',
			'output' => array('filename' => 'name')
		),
		array(
			'service' => 'storage.file.upload.service',
			'input' => array('key' => 'filekey')
		),
		array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('fileid', 'filename', 'mime', 'size'),
			'conn' => 'cbsconn',
			'relation' => '`files`',
			'check' => false,
			'sqlcnd' => "set `name`='\${filename}', `mime`='\${mime}', `size`=\${size} where `fileid`=\${fileid}",
			'escparam' => array('mime', 'filename')
		),
		array(
			'service' => 'gauge.track.write.workflow',
			'input' => array('id' => 'fileid')
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'fileid', 'parent' => 'dirid', 'cname' => 'name', 'pname' => 'dirname'),
			'conn' => 'cbsconn',
			'relation' => '`files`',
			'sqlprj' => '`fileid`, `name`, `mime`, `size`',
			'sqlcnd' => "where `fileid`=\${id}",
			'errormsg' => 'Invalid File ID',
			'type' => 'file',
			'successmsg' => 'File information given successfully',
			'output' => array('entity' => 'file'),
			'auth' => false,
			'track' => false,
			'sinit' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'dirid'),
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
		return array('filename', 'fileid', 'dirid', 'dirname', 'file', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>