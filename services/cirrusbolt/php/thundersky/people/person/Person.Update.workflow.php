<?php 
require_once(SBSERVICE);
require_once(CBQUEUECONF);

/**
 *	@class PersonUpdateWorkflow
 *	@desc Edits person contacts:devices using ID
 *
 *	@param pnid long int Person ID [memory]
 *	@param device string Device to verify [memory] optional default 'mail' ('mail', 'sms')
 *	@param email string Person email [memory] optional default false
 *	@param phone string Person phone [memory] optional default false
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string User name [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PersonUpdateWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'pnid', 'user'),
			'optional' => array('email' => false, 'phone' => false, 'device' => 'mail')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Person updated successfully';
		
		switch($memory['device']){
			case 'mail' :
				$attr = 'email';
				break;
			case 'sms' : 
				$attr = 'phone';
				break;
			default :
				$memory['valid'] = false;
				$memory['msg'] = 'Invalid Type';
				$memory['status'] = 500;
				$memory['details'] = "Person device type : ".$type." is invalid @people.person.update";
				return $memory;
				break;
		}
	
		$workflow = array(
		array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array($attr),
			'input' => array('id' => 'pnid'),
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'sqlcnd' => "set `$attr`='\${".$attr."}' where `pnid`=\${id}",
			'escparam' => array($attr),
			'check' => false,
			'successmsg' => 'Person updated successfully',
			'errormsg' => 'No Change / Invalid Person ID'
		));
		
		if(in_array($memory['device'], explode(':', PERSON_DEVICES))){
		
			array_push($workflow,
			array(
				'service' => 'transpera.relation.update.workflow',
				'args' => array('pnid', 'device'),
				'conn' => 'cbpconn',
				'relation' => '`persons`',
				'sqlcnd' => "set `verify`='', `device`='\${device}' where `pnid`=\${pnid}",
				'escparam' => array('device'),
				'check' => false,
				'errormsg' => 'Invalid Person'
			),
			array(
				'service' => 'guard.key.reset.workflow',
				'input' => array('id' => 'keyid'),
				'context' => CONTEXT
			),
			array(
				'service' => 'guard.chain.track.workflow',
				'input' => array('child' => 'pnid', 'cname' => 'user'),
				'verb' => 'updated devices of',
				'join' => 'in',
				'public' => 0,
				'output' => array('id' => 'trackid')
			),
			array(
				'service' => 'people.person.send.workflow',
				'input' => array('username' => 'pnid'),
				'password' => false
			),
			array(
				'service' => 'invoke.interface.session.workflow'
			));
		}
		
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