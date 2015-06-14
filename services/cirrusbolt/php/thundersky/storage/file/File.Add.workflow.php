<?php 
require_once(SBSERVICE);

/**
 *	@class FileAddWorkflow
 *	@desc Adds new file to directory
 *
 *	@param filename string File name [memory] optional default false
 *	@param name string File name [memory] optional default 'file'
 *	@param ext string File extension [memory] optional default 'file'
 *	@param mime string MIME type [memory] optional default 'application/force-download'
 *	@param size long int Size in bytes [memory] optional default 0
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param dirid long int Directory ID [memory] optional default 0
 *	@param level integer Web level [memory] optional default 1 (directory admin access allowed)
 *	@param owner long int Owner Key ID [memory] optional default keyid
 *	@param filekey string File key [memory] optional default false
 *
 *	@return fileid long int File ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class FileAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user'),
			'optional' => array('dirid' => 0, 'dirname' => '', 'level' => 1, 'owner' => false, 'filename' => false, 'name' => 'storage', 'ext' => 'file', 'mime' => 'application/force-download', 'size' => 0, 'filekey' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$construct = false;
		if($memory['filename'] == false){
			$memory['filename'] = $memory['name'].'.'.$memory['ext'];
		}
		
		if($memory['filekey']){
			$construct = array(
			array(
				'service' => 'storage.directory.info.workflow',
				'input' => array('dirid' => 'parent')
			),
			array(
				'service' => 'storage.file.upload.service',
				'input' => array('path' => 'path'),
				'key' => $memory['filekey'],
				'name' => false
			));
		}
	
		$workflow = array(
		array(
			'service' => 'transpera.entity.add.workflow',
			'args' => array('filename', 'mime', 'size'),
			'input' => array('parent' => 'dirid'),
			'conn' => 'cbsconn',
			'relation' => '`files`',
			'type' => 'file',
			'sqlcnd' => "(`fileid`, `owner`, `name`, `filename`, `mime`, `size`) values (\${id}, \${owner}, '\${filename}',  '\${filename}', '\${mime}', \${size})",
			'escparam' => array('filename', 'mime'),
			'successmsg' => 'File added successfully',
			'output' => array('id' => 'fileid'),
			'construct' => $construct
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
		return array('fileid', 'dirid', 'dirname', 'file', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>