<?php 
require_once(SBSERVICE);
require_once(EXGPACONF);

/**
 *	@class SlotUpdateWorkflow
 *	@desc Updates slot using ID with authentication
 *
 *	@param slotid long int Slot ID [memory]
 *	@param username string Key username [memory]
 *	@param password string Key password [memory]
 *	@param sscx float Xth Percentage [memory] optional default 0.0
 *	@param hscxii float XIIth Percentage [memory] optional default 0.0
 *
 *	@param sgpa1 float SGPA I [memory] optional default 0.0
 *	@param sgpa2 float SGPA II [memory] optional default 0.0
 *	@param sgpa3 float SGPA III [memory] optional default 0.0
 *	@param sgpa4 float SGPA IV [memory] optional default 0.0
 *	@param sgpa5 float SGPA V [memory] optional default 0.0
 *	@param sgpa6 float SGPA VI [memory] optional default 0.0
 *	@param sgpa7 float SGPA VII [memory] optional default 0.0
 *	@param sgpa8 float SGPA VIII [memory] optional default 0.0
 *	@param sgpa9 float SGPA IX [memory] optional default 0.0
 *	@param sgpa10 float SGPA X [memory] optional default 0.0
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param batchid long int Batch ID [memory] optional default 0
 *	@param btname string Batch Name [memory] optional default ''
 *
 *	@return slotid long int Slot ID [memory]
 *	@return batchid long int Batch ID [memory]
 *	@return btname string Batch Name [memory]
 *	@return slot array Slot information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SlotUpdateWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'slotid', 'username', 'password'),
			'optional' => array('batchid' => 0, 'btname' => '', 'sscx' => 0.0, 'hscxii' => 0.0, 'sgpa1' => 0.0, 'sgpa2' => 0.0, 'sgpa3' => 0.0, 'sgpa4' => 0.0, 'sgpa5' => 0.0, 'sgpa6' => 0.0, 'sgpa7' => 0.0, 'sgpa8' => 0.0, 'sgpa9' => 0.0, 'sgpa10' => 0.0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){		
		$memory['public'] = 1;
		$memory['cgpa'] = $memory['ygpa1'] = $memory['ygpa2'] = $memory['ygpa3'] = $memory['ygpa4'] = $memory['ygpa5'] = 0.0;
		$i = $total = 0;
		
		$memory = Snowblozm::run(array(
			'service' => 'executive.batch.info.workflow'
		), $memory);
		
		if(!$memory['valid'])
			return $memory;
		
		$dept = $memory['batch']['dept'];
		$course = $memory['batch']['course'];
		
		$gpa = Snowblozm::get('all_gpa');
		$gpa = $gpa[$dept][$course];
		
		if($memory['sgpa1'] != ''){
			$i+= $gpa[0];
			$total += $memory['sgpa1']*$gpa[0];
			$memory['ygpa1'] = $memory['sgpa1'];
			if($memory['sgpa2'] != ''){
				$i+= $gpa[1];
				$total += $memory['sgpa2']*$gpa[1];
				$memory['ygpa1'] = ($memory['sgpa1']*$gpa[0] + $memory['sgpa2']*$gpa[1])/($gpa[0] + $gpa[1]);
			}
			else 
				$memory['sgpa2'] = 0.0;
		}
		else 
			$memory['sgpa1'] = $memory['sgpa2'] = 0.0;
		
		if($memory['sgpa3'] != ''){
			$i+= $gpa[2];
			$total += $memory['sgpa3']*$gpa[2];
			$memory['ygpa2'] = $memory['sgpa3'];
			if($memory['sgpa4'] != ''){
				$i+= $gpa[3];
				$total += $memory['sgpa4']*$gpa[3];
				$memory['ygpa2'] = ($memory['sgpa3']*$gpa[2] + $memory['sgpa4']*$gpa[3])/($gpa[2] + $gpa[3]);
			}
			else 
				$memory['sgpa4'] = 0.0;
		}
		else 
			$memory['sgpa3'] = $memory['sgpa4'] = 0.0;
		
		if($memory['sgpa5'] != ''){
			$i+= $gpa[4];
			$total += $memory['sgpa5']*$gpa[4];
			$memory['ygpa3'] = $memory['sgpa5'];
			if($memory['sgpa6'] != ''){
				$i+= $gpa[5];
				$total += $memory['sgpa6']*$gpa[5];
				$memory['ygpa3'] = ($memory['sgpa5']*$gpa[4] + $memory['sgpa6']*$gpa[5])/($gpa[4] + $gpa[5]);
			}
			else 
				$memory['sgpa6'] = 0.0;
		}
		else 
			$memory['sgpa5'] = $memory['sgpa6'] = 0.0;
		
		if($memory['sgpa7'] != ''){
			$i+= $gpa[6];
			$total += $memory['sgpa7']*$gpa[6];
			$memory['ygpa4'] = $memory['sgpa7'];
			if($memory['sgpa8'] != ''){
				$i+= $gpa[7];
				$total += $memory['sgpa8']*$gpa[7];
				$memory['ygpa4'] = ($memory['sgpa7']*$gpa[6] + $memory['sgpa8']*$gpa[7])/($gpa[6] + $gpa[7]);
			}
			else 
				$memory['sgpa8'] = 0.0;
		}
		else 
			$memory['sgpa7'] = $memory['sgpa8'] = 0.0;
		
		if($memory['sgpa9'] != ''){
			$i+= $gpa[8];
			$total += $memory['sgpa9']*$gpa[8];
			$memory['ygpa5'] = $memory['sgpa9'];
			if($memory['sgpa10'] != ''){
				$i+= $gpa[9];
				$total += $memory['sgpa10']*$gpa[9];
				$memory['ygpa5'] = ($memory['sgpa9']*$gpa[8] + $memory['sgpa10']*$gpa[9])/($gpa[8] + $gpa[9]);
			}
			else 
				$memory['sgpa10'] = 0.0;
		}
		else 
			$memory['sgpa9'] = $memory['sgpa10'] = 0.0;
		
		if($i)
			$memory['cgpa'] = ($total)/$i;
		
		$workflow = array(
		array(
			'service' => 'guard.key.authenticate.workflow',
			'input' => array('user' => 'username', 'key' => 'password')
		),
		array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('sscx', 'hscxii', 'cgpa', 'sgpa1', 'sgpa2', 'sgpa3', 'sgpa4', 'sgpa5', 'sgpa6', 'sgpa7', 'sgpa8', 'sgpa9', 'sgpa10', 'ygpa1', 'ygpa2', 'ygpa3', 'ygpa4', 'ygpa5'),
			'input' => array('id' => 'slotid', 'cname' => 'username', 'parent' => 'batchid', 'pname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`slots`',
			'type' => 'slot',
			'sqlcnd' => "set `sscx`=\${sscx}, `hscxii`=\${hscxii}, `cgpa`=\${cgpa}, `sgpa1`=\${sgpa1}, `sgpa2`=\${sgpa2}, `sgpa3`=\${sgpa3}, `sgpa4`=\${sgpa4}, `sgpa5`=\${sgpa5}, `sgpa6`=\${sgpa6}, `sgpa7`=\${sgpa7}, `sgpa8`=\${sgpa8}, `sgpa9`=\${sgpa9}, `sgpa10`=\${sgpa10}, `ygpa1`=\${ygpa1}, `ygpa2`=\${ygpa2}, `ygpa3`=\${ygpa3}, `ygpa4`=\${ygpa4}, `ygpa5`=\${ygpa5} where `slotid`=\${id}",
			'check' => false,
			'successmsg' => 'Slot edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'slotid', 'parent' => 'batchid', 'cname' => 'name', 'pname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`slots`',
			'sqlcnd' => "where `slotid`=\${id}",
			'errormsg' => 'Invalid Slot ID',
			'type' => 'slot',
			'successmsg' => 'Slot information given successfully',
			'output' => array('entity' => 'slot'),
			'auth' => false,
			'track' => false,
			'sinit' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'batchid'),
			'output' => array('chain' => 'pchain')
		));
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
		
		if($memory['sqlrc']){
			$slot = $memory['slot'];
			Snowblozm::run(array(
				'service' => 'people.person.alert.workflow',
				'input' => array('chainid' => 'batchid', 'queid' => 'slotid'),
				'subject' => '[TPO Portal Alerts] '.$memory['btname'].' Slots Updated : '.$memory['username'],
				'body' => 'User '.$memory['username'].' updated Slots. The new slots are as follows :<br />
				<table><tbody><tr><td>Roll No</td><td>'.$slot['rollno'].'</td></tr>
				<tr><td>Username</td><td>'.$slot['username'].'</td></tr>
				<tr><td>X %</td><td>'.$slot['sscx'].'</td></tr>
				<tr><td>XII %</td><td>'.$slot['hscxii'].'</td></tr>
				<tr><td>CGPA</td><td>'.$slot['cgpa'].'</td></tr>
				<tr><td>Details</td><td>
					<table><thead><tr><th>Part</th><th>SGPA Odd</th><th>SGPA Even</th><th>YGPA</th></tr></thead><tbody>
					<tr><td>1</td><td>'.$slot['sgpa1'].'</td><td>'.$slot['sgpa2'].'</td><td>'.$slot['ygpa1'].'</td></tr>
					<tr><td>2</td><td>'.$slot['sgpa3'].'</td><td>'.$slot['sgpa4'].'</td><td>'.$slot['ygpa2'].'</td></tr>
					<tr><td>3</td><td>'.$slot['sgpa5'].'</td><td>'.$slot['sgpa6'].'</td><td>'.$slot['ygpa3'].'</td></tr>
					<tr><td>4</td><td>'.$slot['sgpa7'].'</td><td>'.$slot['sgpa8'].'</td><td>'.$slot['ygpa4'].'</td></tr>
					<tr><td>5</td><td>'.$slot['sgpa9'].'</td><td>'.$slot['sgpa10'].'</td><td>'.$slot['ygpa5'].'</td></tr>
					</tbody></table>
				</td></tr></tbody></table>'
			), $memory);
		}
		
		$memory['padmin'] = $memory['admin'];
		$memory['admin'] = 1;
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('slotid', 'batchid', 'btname', 'slot', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>
