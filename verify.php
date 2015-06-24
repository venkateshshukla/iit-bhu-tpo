<?php 

	/**
	 * 	@initialize TPO
	**/
	include_once('init.php');
	
	/**
	 *	@initialize $memory
	**/
	$memory['reqtype'] = isset($_GET['enc']) ? $_GET['enc'] : 'get';
	if(!isset($_GET['username']) || !isset($_GET['verify'])){
		echo '<h1>Invalid Request</h1>';
		exit;
	}
	
	/**
	 *	@launch Verify Service
	**/
	$memory = Snowblozm::run(array(
		'service' => 'invoke.launch.workflow.workflow',
		'message' => array(
			'service' => 'people.person.verify.workflow',
			'username' => $_GET['username'],
			'verify' => $_GET['verify']
		)
	), $memory);
	
	if($memory['message']['valid']){
		echo '<h2>Account Verified Successfully</h2><p>You may now login <a href="'.ROOTPATH.'/#!/view/#login/">here</a>.</p>';
	}
	else {
		echo '<h2>System Error</h2><p>Please report with the following details to <a href="web.tpo@itbhu.ac.in">web.tpo@itbhu.ac.in</a>. Thank you for your support and understanding.</p><p>'.json_encode(array(
			'msg' => $memory['msg'],
			'details' => $memory['details']
		)).'</p>';
	}
	
?>
