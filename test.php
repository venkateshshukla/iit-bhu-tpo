<?php 
	
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	$headers = "From: Adhyayan IT BHU <web.tpo@itbhu.ac.in>\r\nReply-To: Adhyayan IT BHU <adhyayan@itbhu.ac.in>\r\nX-Mailer: PHP/".phpversion();
	if(mail("vibhaj.rajan.cse08@itbhu.ac.in", "Testing", "Hello World!", $headers, "-fAdhyayan IT-BHU <web.tpo@itbhu.ac.in>"))
		echo "Mail Sent";
	else echo "Mail Failed";
	
	exit;
	
	/**
	 * 	@initialize TPO
	**/
	include_once('init.php');

	/**
	 *	@launch Service
	**/
	$memory = Snowblozm::run(array(
		'service' => 'transpera.relation.select.workflow',
		'conn' => 'exconn',
		'relation' => '`students`',
		'sqlcnd' => 'where `stdid`>6 and `slot`=0',
		'ismap' => false
	), $memory);
	
	$memory['user'] = 'tpo.iitbhu';
	$memory['keyid'] = 10;
	
	foreach($memory['result'] as $std){
		$student = Snowblozm::execute(array(
		array(
			'service' => 'guard.chain.info.workflow',
			'chainid' => $std['stdid']
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('chain'),
			'params' => array('chain.parent' => 'batchid')
		),
		array(
			'service' => 'executive.slot.add.workflow',
			'owner' => $std['owner'],
			'username' => $std['username'],
			'rollno' => $std['rollno']
		),
		array(
			'service' => 'transpera.relation.update.workflow',
			'args' => array('slotid'),
			'conn' => 'exconn',
			'relation' => '`students`',
			'sqlcnd' => "set `slot`=\${slotid} where `stdid`=".$std['stdid']
		)), $memory);
		
		echo json_encode(array($std['username'], $student['details']));
	}
	
?>
