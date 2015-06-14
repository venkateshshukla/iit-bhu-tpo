<?php 
require_once(SBSERVICE);

/**
 *	@class CutoffRemoveWorkflow
 *	@desc Removes cutoff by ID
 *
 *	@param ctfid long int Cutoff ID [memory]
 *	@param keyid long int Usage Key ID [memory]
 *	@param visitid long int Visit ID [memory] optional default 0
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class CutoffRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'ctfid'),
			'optional' => array('visitid' => 0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$service = array(
			'service' => 'transpera.entity.remove.workflow',
			'input' => array('id' => 'ctfid', 'parent' => 'visitid'),
			'conn' => 'exconn',
			'relation' => '`cutoffs`',
			'type' => 'cutoff',
			'sqlcnd' => "where `ctfid`=\${id}",
			'errormsg' => 'Invalid Cutoff ID',
			'successmsg' => 'Cutoff removed successfully'
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