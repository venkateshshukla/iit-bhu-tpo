<?php 

	/**
	 * 	@initialize TPO
	**/
	include_once('init.php');
	
	/**
	 *	@launch Register Service
	**/
	$memory = Snowblozm::run(array(
		'service' => 'executive.student.register.workflow',
	), array_merge($memory, $_POST));
	
	if($memory['valid']){
		echo '<html><head>
					<meta http-equiv="Refresh" content="15; url='.ROOTPATH.'" />
				</head><body><h2>Registered Successfully. Verification Pending. Please Check Your Inbox.</h2><p>In case you did not receive any verification mail, you may resend it from the Portal.</p><p>Redirecting ...</p></body></html>';
	}
	else {
		echo '<html><head>
					<!--<meta http-equiv="Refresh" content="15; url='.ROOTPATH.'" />-->
				</head><body><h2>Registration Failure</h2><p>Reason : '.json_encode(array('msg' => $memory['msg'], 'details' => $memory['details'])).'</p></body></html>';
				
		/*echo '<h2>Authentication Failure</h2><p>Please report with the following details to <a href="web.tpo@itbhu.ac.in">web.tpo@itbhu.ac.in</a> in case this is incorrect. Thank you for your support and understanding.</p><p>'.json_encode(array(
			'msg' => $memory['msg'],
			'details' => $memory['details']
		)).'</p>';*/
	}
	
?>
