<?php 

	/**
	 * 	@initialize TPO
	**/
	include_once('init.php');
	
	/**
	 *	@launch OpenID Service
	**/
	$memory = Snowblozm::run(array(
		'service' => 'invoke.interface.openid.workflow',
		'openid_identifier' => isset($_GET['openid_identifier']) ? $_GET['openid_identifier'] : false,
		'continue' => isset($_POST['continue']) ? $_POST['continue'] : false
	), $memory);
	
	if($memory['valid']){
		echo '<html><head>
					<meta http-equiv="Refresh" content="1; url='.$memory['continue'].'" />
				</head><body><h2>Authenticated Successfully.</h2><p>Redirecting ...</p></body></html>';
	}
	else {
		echo '<html><head>
					<meta http-equiv="Refresh" content="5; url='.ROOTPATH.'" />
				</head><body><h2>Authentication Failure</h2><p>Redirecting ...</p><p>Reason : '.json_encode(array('msg' => $memory['msg'], 'details' => $memory['details'])).'</p></body></html>';
				
		/*echo '<h2>Authentication Failure</h2><p>Please report with the following details to <a href="web.tpo@itbhu.ac.in">web.tpo@itbhu.ac.in</a> in case this is incorrect. Thank you for your support and understanding.</p><p>'.json_encode(array(
			'msg' => $memory['msg'],
			'details' => $memory['details']
		)).'</p>';*/
	}
	
?>
