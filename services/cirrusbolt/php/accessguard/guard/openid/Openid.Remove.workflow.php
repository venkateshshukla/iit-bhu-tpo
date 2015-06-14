<?php 
require_once(SBSERVICE);

/**
 *	@class OpenidRemoveWorkflow
 *	@desc Removes openid email
 *
 *	@param keyid long int Key ID [memory]
 *	@param email string Email ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class OpenidRemoveWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('email' => false)
		);
	}

	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Openid Email(s) Removed Successfully';
		$qry = '';
		$args = array('keyid');
		$esc = array();
		
		if($memory['email']){
			$qry = "`email`='\${email}' and ";
			array_push($args, 'email');
			array_push($esc, 'email');
		}
		
		$service = array(
			'service' => 'transpera.relation.delete.workflow',
			'args' => $args,
			'conn' => 'cbconn',
			'relation' => '`openids`',
			'sqlcnd' => "where $qry `keyid`=\${keyid}",
			'escparam' => $esc,
			'check' => false,
			'errormsg' => 'Invalid Openid'
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