<?php 
require_once(SBSERVICE);

/**
 *	@class PersonProfileService
 *	@desc Returns person profile information by ID
 *
 *	@param pnid long int Person ID [memory]
 *	@param peopleid long int People ID [memory] optional default PEOPLE_ID
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@return contact array Person contact information [memory]
 *	@return personal array Person personal information [memory]
 *	@return pnid long int Person ID [memory]
 *	@return peopleid long int People ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PersonProfileService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('pnid', 'keyid'),
			'optional' => array('peopleid' => PEOPLE_ID)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['contact'] = array(
			'email' => '',
			'phone' => '',
			'address' => ''
		);
		$memory['personal'] = array(
			'gender' => '',
			'dateofbirth' => ''
		);

		$workflow = array(
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'pnid', 'parent' => 'peopleid'),
			'track' => false,
			'chadm' => false,
			'mgchn' => false,
			'action' => 'con',
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'sqlprj' => '`email`, `phone`, `address`',
			'sqlcnd' => "where `pnid`='\${id}'",
			'errormsg' => 'Invalid Person ID',
			'successmsg' => 'Person contact information given successfully',
			'output' => array('entity' => 'contact')
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'pnid', 'parent' => 'peopleid'),
			'track' => false,
			'chadm' => false,
			'mgchn' => false,
			'action' => 'per',
			'conn' => 'cbpconn',
			'relation' => '`persons`',
			'sqlprj' => '`gender`, `dateofbirth`',
			'sqlcnd' => "where `pnid`='\${id}'",
			'errormsg' => 'Invalid Person ID',
			'successmsg' => 'Person personal information given successfully',
			'output' => array('entity' => 'personal'),
			'strict' => false
		));
		//Snowblozm::$debug = true;
		$memory = Snowblozm::execute($workflow, $memory);

		$memory['valid'] = true;
		$memory['msg'] = 'Person profile information given successfully';
		$memory['status'] = 200;
		$memory['details'] = "Successfully executed";
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('contact', 'personal', 'pnid', 'peopleid');
	}
	
}

?>