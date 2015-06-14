<?php 
require_once(SBSERVICE);

/**
 *	@class HumanRecaptchaService
 *	@desc Verifies reCAPTCHA service for human check
 *
 *	@param recaptcha_challenge_field string Challenge [memory]
 *	@param recaptcha_response_field string Response [memory] 
 *	@param recaptcha array reCAPTCHA configuration [Snowblozm] (url, public, private)
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class HumanRecaptchaService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('recaptcha_challenge_field', 'recaptcha_response_field')
		);
	}

	/**
	 *	@interface Service
	**/
	public function run($memory){
		$config = Snowblozm::get('recaptcha');
		
		$service = array(
			'service' => 'queue.curl.execute.service',
			'post' => true,
			'url' => $config['url'],
			'data' => array(
				'privatekey' => $config['private'],
				'remoteip' => $_SERVER['REMOTE_ADDR'],
				'challenge' => $memory['recaptcha_challenge_field'],
				'response' => $memory['recaptcha_response_field']
			)
		);
		
		$memory = Snowblozm::run($service, $memory);
		
		if(!$memory['valid']){
			$memory['valid'] = false;
			$memory['msg'] = 'Error verifying reCAPTCHA';
			$memory['status'] = 501;
			$memory['details'] = 'Error : in cURL request @human.recaptcha.service '.$memory['details'];
			return $memory;
		}
		
		$result = split("\n", $memory['response']);
		if(trim($result[0]) != 'true'){
			$memory['valid'] = false;
			$memory['msg'] = 'Invalid reCAPTCHA';
			$memory['status'] = 503;
			$memory['details'] = 'Error : '.$result[1].' in reCAPTCHA verification @human.recaptcha.service';
			return $memory;
		}
		
		$memory['valid'] = true;
		$memory['msg'] = 'reCAPTCHA Verified Successfully';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed. cURL Response : '.$memory['response'];
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array();
	}
	
}

?>