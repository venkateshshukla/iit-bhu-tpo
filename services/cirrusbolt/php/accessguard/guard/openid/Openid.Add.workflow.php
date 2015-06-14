<?php 
require_once(SBSERVICE);

/**
 *	@class OpenidAddWorkflow
 *	@desc Adds openid email
 *
 *	@param keyid long int Key ID [memory]
 *	@param email string Email ID [memory]
 *
 *	@return return oid long int Openid ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class OpenidAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'email')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Openid Email Added Successfully';
		
		$service = array(
			'service' => 'transpera.relation.insert.workflow',
			'args' => array('email', 'keyid'),
			'conn' => 'cbconn',
			'relation' => '`openids`',
			'sqlcnd' => "(`email`, `keyid`) values ('\${email}', \${keyid})",
			'escparam' => array('email'),
			'output' => array('id' => 'oid')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('oid');
	}
	
}

?>