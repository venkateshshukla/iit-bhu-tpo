<?php 
	require_once('../../init.php');
	
	$service = array(
		'service' => 'transpera.relation.select.workflow',
		'conn' => 'ayconn',
		'relation' => '`profiles`',
		'sqlprj' => 'count(`plid`) as `total`',
		'sqlcnd' => "where `ustatus`='A' ",
		'type' => 'profile',
		'successmsg' => 'Registration count information given successfully',
		'output' => array('result' => 'profiles'),
		'ismap' => false
	);
	
	$memory = Snowblozm::run($service, $memory);
	$total = $memory['profiles'][0]['total'];
	
	$service = array(
		'service' => 'transpera.relation.select.workflow',
		'conn' => 'ayconn',
		'relation' => '`numberstory`',
		'sqlprj' => '`story`',
		'sqlcnd' => "where `number`=$total",
		'type' => 'number',
		'ismap' => false
	);
	
	$memory = Snowblozm::run($service, $memory);
	
	$result = array(
		'total' => $total,
		'story' => $memory['result'][0]['story'],
		'events' => array(
			'Parikshan' => array(
				'event' => 'Online Trial Round',
				'time' => mktime(22, 0, 0, 3, 12, 2012),
				'url' => '2012/parikshan/',
				'id' => 'parikshan-trial'
			),
			'Prayaas (Algorithm)' => array(
				'event' => 'Online Trial Run',
				'time' => mktime(22, 0, 0, 3, 13, 2012),
				'url' => '2012/prayaas-algorithm/',
				'id' => 'prayaas-algorithm-trial'
			),
			'Prayaas (Mathematical)' => array(
				'event' => 'Online Trial Run',
				'time' => mktime(22, 0, 0, 3, 14, 2012),
				'url' => '2012/prayaas-mathematical/',
				'id' => 'prayaas-mathematical-trial'
			),
			'Pratyay' => array(
				'event' => 'Full Paper Submission',
				'time' => mktime(22, 0, 0, 3, 18, 2012),
				'url' => '2012/conference/',
				'id' => 'pratyay-submission'
			),
			'Pradarshan' => array(
				'event' => 'Abstract Submission',
				'time' => mktime(22, 0, 0, 3, 20, 2012),
				'url' => '2012/pradarshan/',
				'id' => 'pradarchan-submission'
			)
		)
	);
	
	echo json_encode($result);
	exit;
	
?>
