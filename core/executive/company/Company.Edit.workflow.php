<?php 
require_once(SBSERVICE);

/**
 *	@class CompanyEditWorkflow
 *	@desc Edits company using ID
 *
 *	@param comid long int Company ID [memory]
 *	@param name string Company name [memory]
 *	@param site string Website [memory]
 
 *	@param title string Title [memory]
 *	@param phone string Phone [memory]
 *	@param address string Address [memory] 
 *	@param country string Country [memory]
 *	@param location long int Location [memory] optional default 0
 *	@param dateofbirth string Date of birth [memory] (Format YYYY-MM-DD)
 *	@param gender string Gender [memory]  (M=Male F=Female N=None)
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Username [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class CompanyEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'comid', 'name', 'phone', 'address', 'site', 'page'),
			'optional' => array('location' => 0, 'title' => '', 'gender' => 'N', 'dateofbirth' => '')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$workflow = array(
		array(
			'service' => 'people.person.edit.workflow',
			'input' => array('pnid' => 'comid'),
			'country' => 'India',
		),
		array(
			'service' => 'people.person.update.workflow',
			'input' => array('pnid' => 'comid'),
			'email' => false,
			'device' => 'sms'
		),
		array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('comid', 'name', 'site', 'page'),
			'conn' => 'exconn',
			'relation' => '`companies`',
			'sqlcnd' => "set `name`='\${name}', `site`='\${site}', `page`='\${page}' where `comid`=\${comid}",
			'successmsg' => 'Company edited successfully',
			'check' => false,
			'escparam' => array('name', 'site', 'page'),
			'errormsg' => 'No Change / Invalid Company ID'
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>