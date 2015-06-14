<?php 
require_once(SBSERVICE);

/**
 *	@class WillingnessUpdateWorkflow
 *	@desc Updates willingness using ID
 *
 *	@param wlgsid long int Willingness ID [memory]
 *	@param resume long int Resume [memory] optional default false
 *	@param wstatus integer Status [memory] (0=Eligible 1=Willing -1=Notwilling)
 *	@param keyid long int Usage Key ID [memory]
 *	@param user string Key User [memory]
 *	@param batchid long int Batch ID [memory] optional default 0
 *	@param btname string Batch Name [memory] optional default ''
 *
 *	@return wlgsid long int Willingness ID [memory]
 *	@return batchid long int Batch ID [memory]
 *	@return btname string Batch Name [memory]
 *	@return willingness array Willingness information [memory]
 *	@return chain array Chain information [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class WillingnessUpdateWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'wlgsid', 'wstatus', 'batchid', 'btname'),
			'optional' => array('resume' => 0, 'experience' => false, 'approval' => 0, 'me' => 'me')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['public'] = 1;
		$qry = '';
		
		if($memory['resume']) $qry .= "`resume`=\${resume}, ";
		if($memory['experience']) $qry .= "`experience`='\${experience}', ";
		if($memory['approval'] && ($memory['approval'] > 1 || $memory['approval'] < -1)) $qry .= "`approval`=\${approval}, ";
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('resume', 'wstatus', 'experience', 'approval'),
			'input' => array('id' => 'wlgsid', 'cname' => 'name', 'parent' => 'batchid', 'btname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`willingnesses`',
			'type' => 'willingness',
			'sqlcnd' => "set $qry `status`=(case `approval` when 0 then \${wstatus} else `status` end) where `wlgsid`=\${id}",
			'escparam' => array('experience'),
			'check' => false,
			'errormsg' => 'Willingness Not Editable / No Change',
			'successmsg' => 'Willingness edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'wlgsid', 'parent' => 'batchid', 'cname' => 'name', 'btname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`willingnesses` w, `students` s, `grades` g,`visits` v',
			'sqlprj' => "w.`wlgsid`, w.`visitid`, w.`resume`, w.`status`, w.`approval`, w.`experience`, w.`name` as `wname`, w.`batch`, s.`stdid`, s.`username`, s.`name`, s.`email`, s.`rollno`, g.`cgpa`, g.`sscx`, g.`hscxii`, v.`comuser`, v.`comid`, v.`vtype`, v.`year`, v.`visitdate`, v.`vstatus`,  (case (select `course` from `batches` where `btname`=w.`batch`) when 'btech' then v.`bpackage` when 'idd' then v.`ipackage` when 'mtech' then v.`mpackage` else '' end) as `package`",
			'sqlcnd' => "where `wlgsid`=\${id} and w.`owner`=s.`owner` and s.`grade`=g.`gradeid` and v.`visitid`=w.`visitid`",
			'errormsg' => 'Invalid Willingness ID',
			'type' => 'willingness',
			'successmsg' => 'Willingness information given successfully',
			'output' => array('entity' => 'willingness'),
			'auth' => false,
			'track' => false,
			'sinit' => false,
			'cache' => false
		),
		array(
			'service' => 'guard.chain.info.workflow',
			'input' => array('chainid' => 'batchid'),
			'output' => array('chain' => 'pchain')
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('willingness'),
			'params' => array('willingness.batch' => 'btname')
		),
		array(
			'service' => 'executive.batch.find.workflow'
		),
		array(
			'service' => 'storage.file.list.workflow',
			'input' => array('dirid' => 'resumes', 'filter' => 'keyid'),
			'output' => array('files' => 'resumelist', 'admin' => 'fadmin', 'padmin' => 'fpadmin', 'pchain' => 'fpchain')
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
		return array('wlgsid', 'batchid', 'btname', 'willingness', 'chain', 'pchain', 'admin', 'padmin', 'resumelist', 'me');
	}
	
}

?>