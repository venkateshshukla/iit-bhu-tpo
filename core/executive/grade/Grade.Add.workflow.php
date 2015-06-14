<?php 
require_once(SBSERVICE);

/**
 *	@class GradeAddWorkflow
 *	@desc Adds new grade
 *
 *	@param username string Grade username [memory]
 *	@param rollno string Roll No [memory]
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
 *	@param level integer Web level [memory] optional default false (inherit portal admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
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
class GradeAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'username', 'rollno'),
			'optional' => array('batchid' => 0, 'btname' => '', 'level' => false, 'owner' => false, 'sscx' => 0.0, 'hscxii' => 0.0, 'gradcent' => 0.0, 'sgpa1' => 0.0, 'sgpa2' => 0.0, 'sgpa3' => 0.0, 'sgpa4' => 0.0, 'sgpa5' => 0.0, 'sgpa6' => 0.0, 'sgpa7' => 0.0, 'sgpa8' => 0.0, 'sgpa9' => 0.0, 'sgpa10' => 0.0)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['verb'] = 'added';
		$memory['join'] = 'on';
		$memory['public'] = 1;
		$memory['cgpa'] = $memory['ygpa1'] = $memory['ygpa2'] = $memory['ygpa3'] = $memory['ygpa4'] = $memory['ygpa5'] = 0.0;
		$i = $total = 0;
		
		if($memory['sgpa1'] != ''){
			$i++;
			$total += $memory['sgpa1'];
			$memory['ygpa1'] = $memory['sgpa1'];
			if($memory['sgpa2'] != ''){
				$i++;
				$total += $memory['sgpa2'];
				$memory['ygpa1'] = ($memory['sgpa1'] + $memory['sgpa2'])/2;
			}
			else 
				$memory['sgpa2'] = 0.0;
		}
		else 
			$memory['sgpa1'] = $memory['sgpa2'] = 0.0;
		
		if($memory['sgpa3'] != ''){
			$i++;
			$total += $memory['sgpa3'];
			$memory['ygpa2'] = $memory['sgpa3'];
			if($memory['sgpa4'] != ''){
				$i++;
				$total += $memory['sgpa4'];
				$memory['ygpa2'] = ($memory['sgpa3'] + $memory['sgpa4'])/2;
			}
			else 
				$memory['sgpa4'] = 0.0;
		}
		else 
			$memory['sgpa3'] = $memory['sgpa4'] = 0.0;
		
		if($memory['sgpa5'] != ''){
			$i++;
			$total += $memory['sgpa5'];
			$memory['ygpa3'] = $memory['sgpa5'];
			if($memory['sgpa6'] != ''){
				$i++;
				$total += $memory['sgpa6'];
				$memory['ygpa3'] = ($memory['sgpa5'] + $memory['sgpa6'])/2;
			}
			else 
				$memory['sgpa6'] = 0.0;
		}
		else 
			$memory['sgpa5'] = $memory['sgpa6'] = 0.0;
		
		if($memory['sgpa7'] != ''){
			$i++;
			$total += $memory['sgpa7'];
			$memory['ygpa4'] = $memory['sgpa7'];
			if($memory['sgpa8'] != ''){
				$i++;
				$total += $memory['sgpa8'];
				$memory['ygpa4'] = ($memory['sgpa7'] + $memory['sgpa8'])/2;
			}
			else 
				$memory['sgpa8'] = 0.0;
		}
		else 
			$memory['sgpa7'] = $memory['sgpa8'] = 0.0;
		
		if($memory['sgpa9'] != ''){
			$i++;
			$total += $memory['sgpa9'];
			$memory['ygpa5'] = $memory['sgpa9'];
			if($memory['sgpa10'] != ''){
				$i++;
				$total += $memory['sgpa10'];
				$memory['ygpa5'] = ($memory['sgpa9'] + $memory['sgpa10'])/2;
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
			'service' => 'transpera.entity.add.workflow',
			'args' => array('username', 'rollno', 'sscx', 'hscxii', 'gradcent', 'cgpa', 'sgpa1', 'sgpa2', 'sgpa3', 'sgpa4', 'sgpa5', 'sgpa6', 'sgpa7', 'sgpa8', 'sgpa9', 'sgpa10', 'ygpa1', 'ygpa2', 'ygpa3', 'ygpa4', 'ygpa5'),
			'input' => array('parent' => 'batchid', 'cname' => 'btname', 'pname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`grades`',
			'type' => 'grade',
			'authorize' => 'info:add:edit:remove:list',
			'sqlcnd' => "(`gradeid`, `owner`, `username`, `rollno`, `sscx`, `hscxii`, `gradcent`, `cgpa`, `sgpa1`, `sgpa2`, `sgpa3`, `sgpa4`, `sgpa5`, `sgpa6`, `sgpa7`, `sgpa8`, `sgpa9`, `sgpa10`, `ygpa1`, `ygpa2`, `ygpa3`, `ygpa4`, `ygpa5`) values (\${id}, \${owner}, '\${username}', '\${rollno}', \${sscx}, \${hscxii}, \${gradcent}, \${cgpa}, \${sgpa1}, \${sgpa2}, \${sgpa3}, \${sgpa4}, \${sgpa5}, \${sgpa6}, \${sgpa7}, \${sgpa8}, \${sgpa9}, \${sgpa10}, \${ygpa1}, \${ygpa2}, \${ygpa3}, \${ygpa4}, \${ygpa5})",
			'escparam' => array('username', 'rollno'),
			'successmsg' => 'Grade added successfully',
			'output' => array('id' => 'gradeid')
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'gradeid', 'parent' => 'batchid', 'cname' => 'name', 'btname' => 'btname'),
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
			'service' => 'executive.batch.info.workflow',
			'output' => array('chain' => 'pchain')
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
