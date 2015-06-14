<?php 
require_once(SBSERVICE);

/**
 *	@class BatchAddWorkflow
 *	@desc Adds new batch
 *
 *	@param btname string Batch name [memory]
 *	@param dept string Department [memory]
 *	@param course string Course [memory]
 *	@param year integer Year [memory] 
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param portalid long int Portal ID [memory] optional default 0
 *	@param plname string Portal Name [memory] optional default ''
 *	@param level integer Web level [memory] optional default false (inherit portal admin access)
 *	@param owner long int Owner ID [memory] optional default keyid
 *
 *	@return batchid long int Batch ID [memory]
 *	@return portalid long int Portal ID [memory]
 *	@return plname string Portal Name [memory]
 *	@return batch array Batch information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class BatchAddWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'dept', 'course', 'year'),
			'optional' => array('portalid' => STUDENT_PORTAL_ID, 'plname' => '', 'level' => false, 'owner' => false, 'btname' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['verb'] = 'added';
		$memory['join'] = 'on';
		$memory['public'] = 1;
		
		if(!$memory['btname']){
			$memory['btname'] = $memory['course'].'.'.$memory['dept'];
			$year = $memory['year'];
			switch($memory['course']){
				case 'btech' :
					$year -= 4;
					break;
				case 'idd' :
				case 'imd' :
					$year -= 5;
					break;
				case 'mtech' :
					$year -= 2;
					break;
				default :
					break;
			}
			
			$year %= 100;
			$year = (($year == 0) ? '00' : ($year < 10 ? '0'.$year : $year));
			$memory['btname'] .= $year;
		}
		
		$memory['course'] = $memory['course'] == 'imd' ? 'idd' : $memory['course'];
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.add.workflow',
			'args' => array('btname', 'dept', 'course', 'year'),
			'input' => array('parent' => 'portalid', 'cname' => 'btname', 'pname' => 'plname'),
			'conn' => 'exconn',
			'relation' => '`batches`',
			'type' => 'batch',
			'icontrol' => 'add:edit:remove:list:info',
			'sqlcnd' => "(`batchid`, `owner`, `btname`, `dept`, `course`, `year`, `resumes`, `notes`) values (\${id}, \${owner}, '\${btname}', '\${dept}', '\${course}', \${year}, \${resumes}, \${notes})",
			'escparam' => array('btname', 'dept', 'course'),
			'successmsg' => 'Batch added successfully',
			'construct' => array(
				array(
					'service' => 'storage.directory.add.workflow',
					'name' => 'storage/private/resumes/'.$memory['btname'].'/',
					'path' => 'storage/private/resumes/'.$memory['btname'].'/',
					'input' => array('stgid' => 'id', 'grroot' => 'id'),
					'authorize' => 'gradd:remove:edit:grlist',
					'grlevel' => -1,
					'output' => array('dirid' => 'resumes')
				),
				array(
					'service' => 'transpera.reference.add.workflow',
					'type' => 'forum',
					'input' => array('parent' => 'id'),
					'output' => array('id' => 'notes')
				)
			),
			'cparam' => array('resumes', 'notes'),
			'output' => array('id' => 'batchid')
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'batchid', 'parent' => 'portalid', 'cname' => 'name', 'plname' => 'plname'),
			'conn' => 'exconn',
			'relation' => '`batches`',
			'sqlcnd' => "where `batchid`=\${id}",
			'errormsg' => 'Invalid Batch ID',
			'type' => 'batch',
			'successmsg' => 'Batch information given successfully',
			'output' => array('entity' => 'batch'),
			'auth' => false,
			'track' => false,
			'sinit' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'portalid'),
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
		return array('batchid', 'portalid', 'plname', 'batch', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>