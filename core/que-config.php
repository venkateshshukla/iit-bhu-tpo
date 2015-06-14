<?php 

	/**
	 *	@devices People.person.update
	**/
	define('PERSON_DEVICES', 'mail');
	
	/**
	 *	@mail People.person.send
	**/
	define('PERSON_SEND_ADMIN_EMAIL', ', tpo@itbhu.ac.in, vibhaj.itbhu@gmail.com');
	define('PERSON_SEND_MAIL_BODY', 'Hi,
	<br />
<p>Your Training and Placement Registration was successful.</p>
<p>Please verify your account by clicking on the following link :</p>
	
<p><a href="'.ROOTPATH.'/verify.php?username=${username}&verify=${verify}" >'.ROOTPATH.'/verify.php?username=${username}&verify=${verify}</a></p>
	
<p>You may also copy and paste the above link to your browser\'s address bar to verify your account.</p>
	
<p>You may even enter the verification code <strong>${verify}</strong> on the <a href="'.ROOTPATH.'/#!/view/#verify/">Verification Page</a> to verify your account.</p>
<p>Note that your account username is <strong>${username}</strong> and password is <strong>${password}</strong>.</p>
	
Regards,<br />
--<br />
T&P Portal Administrator<br />
<br />
Training and Placement Cell<br />
IIT (BHU) Varanasi - 221005<br />
http://iitbhu.ac.in/tpo/<br />
');
	define('PERSON_SEND_MAIL_SUBJECT', '[T&P] Portal Registration Verification');
	
	/**
	 *	@mail People.person.reset
	**/
	define('PERSON_RESET_MAIL_BODY', 'Hi,
	<br />
<p>Your Training and Placement Account password had been successfully reset.</p>
<p>Please note your new password for the account :</p>
	
<p>Password = <strong>${password}</strong></p>
	
<p>Note that your account username is <strong>${username}</strong>.</p>
	
<p>If you did not actually reset your account, please report it to web.tpo@itbhu.ac.in</p>
	
Regards,<br />
--<br />
T&P Portal Administrator<br />
<br />
Training and Placement Cell<br />
IIT (BHU) Varanasi - 221005<br />
http://iitbhu.ac.in/tpo/<br />
');
	define('PERSON_RESET_MAIL_SUBJECT', '[T&P] Portal Account Password Reset');
	
	/**
	 *	@sms People.person.send
	**/
	define('PERSON_SEND_ADMIN_PHONE', ', 9235312784');
	define('PERSON_SEND_SMS_BODY', '');
	define('PERSON_SEND_SMS_FROM', 'TPO');

?>