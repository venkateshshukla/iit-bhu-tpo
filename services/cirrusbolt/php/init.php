<?php 

	/**
	 * 	@root CirrusBolt
	**/
	define('CBROOT', dirname(__FILE__).'/' );

	/** 
	 *	@constants CirrusBolt
	**/
	define('CBMYSQL', CBROOT . 'aquadew/system/Mysql.class.php');
	
	/** 
	 *	@initialize SnowBlozm
	**/
	require_once(CBROOT. '../../snowblozm/php/init.php');
	require_once(SBCORE);
	
	Snowblozm::add('cbcore', array(
		'root' => CBROOT.'aquadew/core/',
		'location' => 'local'
	));
	
	Snowblozm::add('pool', array(
		'root' => CBROOT.'aquadew/pool/',
		'location' => 'local'
	));
	
	Snowblozm::add('rdbms', array(
		'root' => CBROOT.'aquadew/rdbms/',
		'location' => 'local'
	));
	
	Snowblozm::add('guard', array(
		'root' => CBROOT.'accessguard/guard/',
		'location' => 'local'
	));
	
	Snowblozm::add('transpera', array(
		'root' => CBROOT.'accessguard/transpera/',
		'location' => 'local'
	));
	
	Snowblozm::add('invoke', array(
		'root' => CBROOT.'swiftblaze/invoke/',
		'location' => 'local'
	));
	
	Snowblozm::add('gauge', array(
		'root' => CBROOT.'swiftblaze/gauge/',
		'location' => 'local'
	));
	
	Snowblozm::add('access', array(
		'root' => CBROOT.'thundersky/access/',
		'location' => 'local'
	));
	
	Snowblozm::add('display', array(
		'root' => CBROOT.'thundersky/display/',
		'location' => 'local'
	));
	
	Snowblozm::add('queue', array(
		'root' => CBROOT.'thundersky/queue/',
		'location' => 'local'
	));
	
	Snowblozm::add('shortlist', array(
		'root' => CBROOT.'thundersky/shortlist/',
		'location' => 'local'
	));
	
	Snowblozm::add('storage', array(
		'root' => CBROOT.'thundersky/storage/',
		'location' => 'local'
	));
	
	Snowblozm::add('people', array(
		'root' => CBROOT.'thundersky/people/',
		'location' => 'local'
	));
	
	Snowblozm::add('cypher', array(
		'root' => CBROOT.'vaultguard/cypher/',
		'location' => 'local'
	));
	
	/**
	 *	@dependencies
	**/
	//define('PHPMAILER', CBROOT.'../../../libraries/phpmailer/PHPMailer.class.php');
	//define('LIGHTOPENID', CBROOT .'../../../libraries/lightopenid/LightOpenID.class.php');
	//define('CBQUEUECONF', CBROOT. 'config.php');
	//define('CACHELITE', 'Cache/Lite.php');
	//define('CACHELITEOUTPUT', 'Cache/Lite/Output.php');
	
?>
