<?php 

	class Parikshan {
		public static $rounds, $now, $live, $plive, $before, $after;
	}
	
	Parikshan::$now = time();
	Parikshan::$rounds = array(
		'trial' => array(
			'name' => 'Trial Round',
			'//start' => mktime(22, 0, 0, 3, 12, 2012),
			//'end' => mktime(22, 30, 0, 3, 12, 2012),
			'start' => mktime(23, 00, 0, 3, 9, 2012),
			'end' => mktime(11, 05, 0, 3, 10, 2012),
			'questions' => array('min' => 690, 'total' => 5),
			'eid' => 12,
			'feedback' => 21,
			'result' => true,
			'solutions' => true
		),
		'round1' => array(
			'name' => 'Round 1',
			'start' => mktime(22, 0, 0, 3, 9, 2012),
			'end' => mktime(11, 30, 0, 3, 10, 2012),
			'questions' => array('min' => 690, 'total' => 10),
			'eid' => 13,
			'feedback' => 22,
			'result' => true,
			'solutions' => false
		),
		'round2' => array(
			'name' => 'Round 2',
			'start' => mktime(22, 0, 0, 3, 20, 2012),
			'end' => mktime(22, 30, 0, 3, 20, 2012),
			'questions' => array('min' => 690, 'total' => 40),
			'eid' => 15,
			'feedback' => 23,
			'result' => false,
			'solutions' => false
		)
	);
	
	Parikshan::$before = false;
	Parikshan::$live = false;
	Parikshan::$plive = false;
	Parikshan::$after = array();
	
	foreach(Parikshan::$rounds as $key => $round){
		// Next round
		if(Parikshan::$now <$round['start']){
			Parikshan::$before = $key;
			break;
		}
		// Live Round
		elseif(Parikshan::$now < $round['end']){
			Parikshan::$live = $key;
			Parikshan::$plive = $key;
			break;
		}
		// Partial Live Round
		elseif(Parikshan::$now < ($round['end'] + 150)){
			Parikshan::$plive = $key;
			array_unshift(Parikshan::$after, $key);
		}
		// Ended Round
		else {
			array_unshift(Parikshan::$after, $key);
		}
	}
	
?>