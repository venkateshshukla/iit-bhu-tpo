<?php 
	
	/**
	 * 	@initialize CirrusBolt
	**/
	require_once(EXROOT. 'services/cirrusbolt/php/init.php');
	
	Snowblozm::add('executive', array(
		'root' => EXROOT.'core/executive/',
		'location' => 'local'
	));
	
	Snowblozm::add('manager', array(
		'root' => EXROOT.'core/manager/',
		'location' => 'local'
	));
	
	/**
	 *	@constants System
	**/
	define('PHPMAILER', EXROOT .'dev/libraries/phpmailer/PHPMailer.class.php');
	define('LIGHTOPENID', EXROOT .'dev/libraries/lightopenid/LightOpenID.class.php');
	define('CBQUEUECONF', EXROOT. 'core/que-config.php');
	define('EXGPACONF', EXROOT. 'core/gpa-config.php');
	
?>
