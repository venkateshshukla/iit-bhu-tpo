<?php 
require_once(PHPMAILER);

class Mail {
	private $to, $subject, $message, $mail;
	
	public function __construct($into, $insubject, $inmessage){
		$this->to = $into;
		$this->subject =	$insubject;
		$this->message = $inmessage;
	}
	
	public function send(){
		$mail = new PHPMailer();
		$this->mail = $mail;
		$mail->Priority = 1;
		//$mail->SMTPDebug = true;
		
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = 'ssl';
		$mail->Host = MAIL_HOST;
		$mail->Port = MAIL_PORT;

		$mail->Username = MAIL_EMAIL;
		$mail->Password = MAIL_PASS;

		$mail->AddReplyTo(MAIL_EMAIL, MAIL_USER);
		$mail->From = MAIL_EMAIL;
		$mail->FromName = MAIL_USER;

		$mail->Subject = $this->subject;
		$mail->WordWrap = 50;
		$mail->MsgHTML($this->message);
		
		$rcpts = explode(',', $this->to);
		foreach($rcpts as $rcpt){
			$mail->AddAddress($rcpt, "");
		}

		//$mail->AddAttachment("images/phpmailer.gif");             // attachment
		$mail->IsHTML(true);
	
		//echo json_encode($mail);
		return $mail->Send();
	}
	
	public function getError(){
		return $this->mail->ErrorInfo;
	}
	
}

?>