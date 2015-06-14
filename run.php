<?php 
	
	/**
	 * 	@initialize TPO
	**/
	include_once('init.php');	
	//Snowblozm::$debug = true;
	
	/**
	 * 	@emergency TPO
	**/
	if($EMERGENCY && $_POST['service'] != $YEAR){
		echo json_encode(array('valid' => false, 'msg' => $STATUS, 'status' => 500, 'details' => 'Emergency state', 'message' => array(), 'hash' => ''));
		exit;
	}
	
	/**
	 *	@initialize $memory
	**/
	$memory['reqtype'] = isset($_GET['enc']) ? $_GET['enc'] : 'post';
	
	/**
	 *	@invoke Launch Message
	**/
	$memory = Snowblozm::run(array(
		'service' => 'invoke.launch.message.workflow'
	), $memory);
	
?>
