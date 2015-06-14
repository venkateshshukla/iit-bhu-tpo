<?php 
require_once(SBSERVICE);
require_once(EXGPACONF);

/**
 *	@class GradeEditWorkflow
 *	@desc Edits grade using ID
 *
 *	@param gradeid long int Grade ID [memory]
 *	@param username string Grade username [memory]
 *	@param rollno string Roll No [memory]
 *	@param sscx float Xth Percentage [memory] optional default 0.0
 *	@param hscxii float XIIth Percentage [memory] optional default 0.0
 *	@param sscyear integer SSC Passing Year [memory] optional default 0
 *	@param hscyear integer HSC Passing Year [memory] optional default 0
 *	@param sscboard string SSC Passing Board [memory] optional default ''
 *	@param hscboard string HSC Passing Board [memory] optional default ''
 *	@param gradboard string Graduation Passing Board [memory] optional default ''
 *	@param jee float JEE AIR [memory] optional default 0
 *	@param gate float GATE AIR [memory] optional default 0
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
 *	@param backlogs integer Arrears [memory] optional default 0
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param batchid long int Batch ID [memory] optional default 0
 *	@param btname string Batch Name [memory] optional default ''
 *
 *	@return gradeid long int Grade ID [memory]
 *	@return batchid long int Batch ID [memory]
 *	@return btname string Batch Name [memory]
 *	@return grade array Grade information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class GradeEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'gradeid', 'username', 'rollno'),
			'optional' => array('batchid' => 0, 'btname' => '', 'sscx' => 0.0, 'hscxii' => 0.0, 'gradcent' => 0.0, 'sscyear' => 0, 'hscyear' => 0, 'gradyear' => 0, 'sscboard' =>'', 'hscboard' => '', 'gradboard' => '', 'backlogs' => 0, 'jee' => 0, 'gate' => 0, 'gatescore' => 0, 'gatepercentile' => 0, 'sgpa1' => 0.0, 'sgpa2' => 0.0, 'sgpa3' => 0.0, 'sgpa4' => 0.0, 'sgpa5' => 0.0, 'sgpa6' => 0.0, 'sgpa7' => 0.0, 'sgpa8' => 0.0, 'sgpa9' => 0.0, 'sgpa10' => 0.0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){		
		$memory['public'] = 1;
		$memory['cgpa'] = $memory['ygpa1'] = $memory['ygpa2'] = $memory['ygpa3'] = $memory['ygpa4'] = $memory['ygpa5'] = 0.0;
		$i = $total = 0;
		if($memory['gate'] == '') $memory['gate'] = 0;
		if($memory['gatescore'] == '') $memory['gatescore'] = 0;
		if($memory['gatepercentile'] == '') $memory['gatepercentile'] = 0;
		if($memory['jee'] == '') $memory['jee'] = 0;
		
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
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('username', 'rollno', 'sscx', 'hscxii', 'gradcent', 'sscyear', 'hscyear', 'gradyear', 'sscboard', 'hscboard', 'gradboard', 'backlogs', 'jee', 'gate', 'gatescore', 'gatepercentile', 'cgpa', 'sgpa1', 'sgpa2', 'sgpa3', 'sgpa4', 'sgpa5', 'sgpa6', 'sgpa7', 'sgpa8', 'sgpa9', 'sgpa10', 'ygpa1', 'ygpa2', 'ygpa3', 'ygpa4', 'ygpa5'),
			'input' => array('id' => 'gradeid', 'cname' => 'username', 'parent' => 'batchid', 'pname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`grades`',
			'type' => 'grade',
			'sqlcnd' => "set `username`='\${username}', `rollno`='\${rollno}', `sscx`=\${sscx}, `hscxii`=\${hscxii}, `gradcent`=\${gradcent}, `sscyear`=\${sscyear}, `hscyear`=\${hscyear}, `gradyear`=\${gradyear}, `sscboard`= '\${sscboard}',`hscboard`= '\${hscboard}',`gradboard`= '\${gradboard}',`backlogs`= \${backlogs}, `jee`= '\${jee}', `gate`=\${gate}, `gatescore`=\${gatescore}, `gatepercentile`=\${gatepercentile}, `cgpa`=\${cgpa}, `sgpa1`=\${sgpa1}, `sgpa2`=\${sgpa2}, `sgpa3`=\${sgpa3}, `sgpa4`=\${sgpa4}, `sgpa5`=\${sgpa5}, `sgpa6`=\${sgpa6}, `sgpa7`=\${sgpa7}, `sgpa8`=\${sgpa8}, `sgpa9`=\${sgpa9}, `sgpa10`=\${sgpa10}, `ygpa1`=\${ygpa1}, `ygpa2`=\${ygpa2}, `ygpa3`=\${ygpa3}, `ygpa4`=\${ygpa4}, `ygpa5`=\${ygpa5} where `gradeid`=\${id}",
			'escparam' => array('username', 'rollno', 'jee', 'sscboard', 'hscboard', 'gradboard'),
			'check' => false,
			'init' => false,
			'self' => false,
			'successmsg' => 'Grade edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'gradeid', 'parent' => 'batchid', 'cname' => 'name', 'pname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`grades`',
			'sqlcnd' => "where `gradeid`=\${id}",
			'errormsg' => 'Invalid Grade ID',
			'type' => 'grade',
			'successmsg' => 'Grade information given successfully',
			'output' => array('entity' => 'grade'),
			'auth' => true,
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
			
		$memory['padmin'] = $memory['admin'];
		$memory['admin'] = 1;
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('gradeid', 'batchid', 'btname', 'grade', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>
