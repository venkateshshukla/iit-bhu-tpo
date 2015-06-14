<?php 
require_once(SBSERVICE);

/**
 *	@class BoardEditWorkflow
 *	@desc Edits board using ID
 *
 *	@param boardid long int Board ID [memory]
 *	@param desc string Board desc [memory]
 *	@param bname string Board name [memory]
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class BoardEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'boardid', 'bname', 'desc')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){		
		$memory['public'] = 1;
		
		$service = array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('bname', 'desc'),
			'input' => array('id' => 'boardid', 'cname' => 'bname'),
			'conn' => 'cbdconn',
			'relation' => '`boards`',
			'type' => 'board',
			'sqlcnd' => "set `bname`='\${bname}', `desc`='\${desc}' where `boardid`=\${id}",
			'escparam' => array('bname', 'desc'),
			'successmsg' => 'Board edited successfully'
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