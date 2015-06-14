<?php 
require_once(SBSERVICE);

/**
 *	@class SmsSendmtpService
 *	@desc Sends SMS using SMS Gateway API functions
 *
 *	@param to string To address [memory]
 *	@param from string Sender [memory] 
 *	@param body string Message [memory] 
 *	@param sms array SMS configuration [Snowblozm] (type, host, port, secure, user, email, pass)
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class SmsSendService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('to', 'from', 'body')
		);
	}

	/**
	 *	@interface Service
	**/
	public function run($memory){
		$config = Snowblozm::get('sms');
		$to = $memory['to'];
		$from = $memory['from'];
		$body = $memory['body'];
		
		if(count(explode(',', $to)) == 1)
			$url = $config['single'];
		else
			$url = $config['multiple'];
		
		$workflow = array(
		array(
			'service' => 'cbcore.data.substitute.service',
			'args' => array('to', 'from', 'body'),
			'data' => $url,
			'output' => array('result' => 'url')
		),
		array(
			'service' => 'queue.curl.execute.service'
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		
		if(!$memory['valid']){
			$memory['valid'] = false;
			$memory['msg'] = 'Error sending SMS';
			//$memory['status'] = 503;
			//$memory['details'] = 'Error : '.$mail->ErrorInfo.' @mail.smtp.service';
			return $memory;
		}
		
		$memory['valid'] = true;
		$memory['msg'] = 'SMS Accepted for Delivery';
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