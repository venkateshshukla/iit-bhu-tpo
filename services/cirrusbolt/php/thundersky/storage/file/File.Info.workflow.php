<?php 
require_once(SBSERVICE);

/**
 *	@class FileInfoWorkflow
 *	@desc Returns file information by ID
 *
 *	@param fileid long int File ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param dirid long int Directory ID [memory] optional default 0
 *	@param dirname string Directory name [memory] optional default ''
 *
 *	@return fileid long int File ID [memory]
 *	@return dirid long int Directory ID [memory]
 *	@return dirname string Directory name [memory]
 *	@return owner long int Owner [memory]
 *	@return name string File name [memory]
 *	@return filename string File name [memory]
 *	@return mime string MIME [memory]
 *	@return size long int Size in bytes [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class FileInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('fileid'),
			'optional' => array('keyid' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'File information given successfully';
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'fileid'),
			'conn' => 'cbsconn',
			'relation' => '`files`',
			'sqlcnd' => "where `fileid`=\${id}",
			'errormsg' => 'Invalid File ID',
			'output' => array('entity' => 'file')
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('file', 'chain'),
			'params' => array('file.filename' => 'filename', 'file.mime' => 'mime', 'file.size' => 'size', 'file.name' => 'name', 'file.owner' => 'owner', 'chain.parent' => 'parent')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('fileid', 'file', 'owner', 'name', 'filename', 'mime', 'size', 'chain', 'parent');
	}
	
}

?>