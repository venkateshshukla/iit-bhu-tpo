<?php 
require_once(SBSERVICE);

/**
 *	@class PersonCreateWorkflow
 *	@desc Adds new person to people container
 *
 *	@param name string Person name [memory]
 *	@param username string Person username [memory]
 *	@param recaptcha_challenge_field string Challenge [memory]
 *	@param recaptcha_response_field string Response [memory] 
 *	@param country string Country [memory]
 *	@param email string Email [memory] optional default false
 *	@param phone string Phone [memory] optional default false
 *	@param device string Device to verify [memory] optional default 'mail' ('mail', 'sms')
 *	@param location long int Location [memory] optional default 0
 *	@param keyid long int Usage Key [memory] optional default -1
 *	@param user string User name [memory] optional default ''
 *	@param peopleid long int People ID [memory] optional default 0
 *	@param level integer Web level [memory] optional default 1 (people admin access allowed)
 *
 *	@param pname string Parent name [memory] optional default ''
 *	@param verb string Activity verb [memory] optional default 'added'
 *	@param join string Activity join [memory] optional default 'to'
 *	@param public integer Public log [memory] optional default 0
 *	@param human boolean Check human [memory] optional default true
 *	@param authorize string Auth control [memory] optional default 'add:remove:edit:list:con:per:rel:sub:act:eme:pbinfo'
 *
 *	@return pnid long int Person ID [memory]
 *	@!return owner long int Key ID [memory]
 *	@!return password string Password [memory] 
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class PersonCreateWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('name', 'username', 'recaptcha_challenge_field', 'recaptcha_response_field'),
			'optional' => array(
				'keyid' => -1, 
				'user' => '',
				'country' => 'India',
				'email' => false, 
				'phone' => false, 
				'peopleid' => PEOPLE_ID, 
				'level' => 1, 
				'location' => 0, 
				'device' => 'mail', 
				'pname' => '',
				'verb' => 'added',
				'join' => 'to',
				'public' => 0,
				'human' => true,
				'authorize' => 'add:remove:edit:list:con:per:rel:sub:act:eme:pbinfo'
			)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Registration was successful. Please verify your registration.';
		$memory['portalid'] = PEOPLE_ID;
		$memory['level'] = 1;
		$memory['verb'] = 'registered';
		$memory['join'] = 'on';
		$memory['public'] = 1;
		
		$memory = Snowblozm::run(array(
			'service' => 'people.person.add.workflow'
		), $memory);
		
		if(!$memory['valid'])
			return $memory;
			
		$memory = Snowblozm::run(array(
			'service' => 'people.person.send.workflow'
		), $memory);
		
		if($memory['valid'])
			$memory['msg'] = 'Registration was successful. Verification sent successfully.';
		else
			$memory['msg'] = 'Registration was successful. Error sending verification message.<br />Please resend verification mail <a href="#/view/#verify" class="navigate">here</a>';
		
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('pnid');
	}
	
}

?>