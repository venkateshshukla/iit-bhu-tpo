<?php 
	
	/**
	 *	@config Defaults
	**/
	$YEAR = '2015';
	
	$GRADE_FREEZE = true;
	$ADMIN_ACC = 'tpo.itbhu';
	
	$DB_HOST = 'localhost';
	$DB_USER = 'itbhunet_tpoiit';
	$DB_PASS = '[JW[~[Z-(ga@';
	
	$CB_DB_NAME = 'itbhunet_tpo_iit';
	$CBD_DB_NAME = 'itbhunet_tpo_iit';
	$CBQ_DB_NAME = 'itbhunet_tpo_iit';
	$CBS_DB_NAME = 'itbhunet_tpo_iit';
	$CBP_DB_NAME = 'itbhunet_tpo_iit';
	$CBSL_DB_NAME = 'itbhunet_tpo_iit';
	$DB_NAME = 'itbhunet_tpo_iit';
	$DB_PERSIST = true;

	$CACHE_ENABLE = true;
	$CACHE_LIFE = 5;
	$CACHE_LEVEL = 0;
	
	$MAIL_HOST = 'smtp.gmail.com';
	$MAIL_USER = 'Training and Placement Cell - IIT BHU Varanasi';
	$MAIL_EMAIL = 'web.tpo@itbhu.ac.in';
	$MAIL_PASS = 'w3bTPO@itbhu';
	
	$TILES_0 = $TILES_1 = $HTML = $MAINMENU = $STATEMENU = $SHTML = '';
	$TPR = false;
	$PORTAL = '';
	
	/**
	 *	@constants System
	**/
	define('COOKIEKEY', 'executive-session');
	define('COOKIEEXPIRY', 15);
	define('ROOTPATH', 'http://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_NAME'] == 'tpo.iitbhu.org.in' ? '' : '/tpo'));
	define('CONTEXT', 'EX');
	define('UIBASE', EXROOT. 'ui/');
	define('CACHELITE', EXROOT. 'pear/Cache/Lite.php');
	define('CACHELITEOUTPUT', EXROOT. 'pear/Cache/Lite/Output.php');
	define('PLACEMENT_UPDATES_BOARD', 15);
	define('INTERNSHIP_UPDATES_BOARD', 14);
	define('GENERAL_UPDATES_BOARD', 13);
	define('TPR_UPDATES_BOARD', 19);
	define('PERSON_THUMB', 10);
	define('PEOPLE_ID', 16);
	define('STUDENT_PORTAL_ID', 16);
	define('COMPANY_PORTAL_ID', 17);
	define('MANAGER_ID', 18);
	define('TPO_KEY', 10);
	define('FORM_ID', 32);
	define('FORUM_ID', 33);
	define('FORUM_MAIL_SUBJECT_PREFIX', '[TPO Portal Discussion Alerts]');
	define('FORUM_MAIL_BODY_SIGNATURE', 'TPO Portal Alerts<br />http://itbhu.org.in/tpo/');
	define('CURRENT_YEAR', $YEAR);
	define('GRADES_FREEZE', false);
	
	date_default_timezone_set('Asia/Kolkata');
	ini_set('include_path', EXROOT. 'pear/' . PATH_SEPARATOR . ini_get('include_path'));
	
	/**
	 *	@data Defaults
	**/
	$PAGES = array(
		'info' => 'info',
		'error' => 'error'
	);
	
	$FEEDBACKMAILS = 'vibhaj.rajan.cse08@itbhu.ac.in';
	
?>
