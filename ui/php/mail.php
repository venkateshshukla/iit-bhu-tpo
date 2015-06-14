<?php 
	require_once('../../init.php');
	
	if(!in_array($memory['user'], array(
		'tpo.itbhu', 
		'web.tpo', 
		'vibhaj'
	))){
		echo "Not Authorized";
		exit;
	}
	
	if(isset($_POST['submit'])){
		$sub = stripslashes($_POST['sub']);
		$msg = stripslashes($_POST['body']);

		$mail = Snowblozm::get('mail');
		$headers = "From: IT BHU Training and Placement Cell <tpo@itbhu.ac.in>\r\nReply-To: IT BHU Training and Placement Cell <tpo@itbhu.ac.in>\r\nX-Mailer: PHP/".phpversion();

		
		$to = explode(',', $_POST['to']);

		foreach($to as $email){
			//echo json_encode(array($to, $sub, $msg, $headers));
			if(mail($email,$sub,$msg,$headers, "-fIT BHU Training and Placement Cell <tpo@itbhu.ac.in>"))
				echo "Mail sent successfully to $email<br />";
			else
				echo "Error sending mail to $email<br />";
		}
	}
	else {
		echo 'Invalid Request';
	}
	exit;
	
?>
