<?php 
require_once(SBSERVICE);
require_once(PHPMAILER);

/**
 *	@class MailSmtpService
 *	@desc Sends HTML mail using PHPMailer SMTP functions
 *
 *	@param to string To address [memory]
 *	@param subject string Subject [memory] 
 *	@param body string Message [memory] 
 *	@param attach array Attachments [memory] optional default array()
 *	@param custom array Headers [memory] optional default array()
 *	@param mail array Mail configuration [Snowblozm] (type, host, port, secure, user, email, pass)
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *	
**/
class MailSmtpService implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('to', 'subject', 'body'),
			'optional' => array('attach' => array(), 'custom' => array())
		);
	}

	/**
	 *	@interface Service
	**/
	public function run($memory){
		$config = Snowblozm::get('mail');
		$to = $memory['to'];
		$subject = $memory['subject'];
		$body = $memory['body'];
		
		$mail = new PHPMailer();
		$mail->IsSMTP();
		//$mail->SMTPDebug  = 1;
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = $config['secure'];
		$mail->Host = $config['host'];
		$mail->Port = $config['port'];

		$mail->Username = $config['email'];
		$mail->Password = $config['pass'];

		$mail->AddReplyTo($config['email'], $config['user']);
		$mail->From = $config['email'];
		$mail->FromName = $config['user'];
		
		foreach($memory['custom'] as $custom){
			$mail->addCustomHeader($custom);
		}
		
		$rcpts = explode(',', $to);
		foreach($rcpts as $rcpt){
			$rcpt = trim($rcpt);
			$parts = explode('<', $rcpt);
			
			if(count($parts) == 1)
				$mail->AddAddress($parts[0]);
			else
				$mail->AddAddress(substr($parts[1], 0, -1), trim($parts[0]));
		}

		$mail->Subject = $subject;
		$mail->WordWrap = 50;
		$mail->MsgHTML($body);
		
		foreach($memory['attach'] as $key => $attach){
			if(!$mail->AddAttachment($attach)){
				$memory['valid'] = false;
				$memory['msg'] = 'Error Attaching File';
				$memory['status'] = 504;
				$memory['details'] = 'Error attaching file : '.$attach.' @mail.smtp.service';
				return $memory;
			}
		}

		$mail->IsHTML(true);

		if(!@$mail->Send()) {
			$memory['valid'] = false;
			$memory['msg'] = 'Error sending Mail';
			$memory['status'] = 503;
			$memory['details'] = 'Error : '.$mail->ErrorInfo.' @mail.smtp.service';
			return $memory;
		}
		
		$memory['valid'] = true;
		$memory['msg'] = 'Mail Accepted for Delivery';
		$memory['status'] = 200;
		$memory['details'] = 'Successfully executed';
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