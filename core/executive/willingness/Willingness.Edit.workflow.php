<?php 
require_once(SBSERVICE);

/**
 *	@class WillingnessEditWorkflow
 *	@desc Edits willingness using ID
 *
 *	@param wlgsid long int Willingness ID [memory]
 *	@param name string Name [memory]
 *	@param approval integer Approval [memory] (0=Pending 1=Positive -1=Negative)
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
class WillingnessEditWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'user', 'wlgsid', 'approval', 'batchid', 'btname'),
			'optional' => array('experience' => false, 'me' => 'info')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['public'] = 1;
		$qry = '';
		
		if($memory['experience']) $qry .= "`experience`='\${experience}', ";
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.edit.workflow',
			'args' => array('approval', 'experience'),
			'input' => array('id' => 'wlgsid', 'cname' => 'name', 'parent' => 'batchid', 'btname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`willingnesses`',
			'type' => 'willingness',
			'sqlcnd' => "set $qry `approval`=\${approval} where `wlgsid`=\${id}",
			'escparam' => array('experience'),
			'init' => false,
			'self' => false,
			'check' => false,
			'successmsg' => 'Willingness edited successfully'
		),
		array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'wlgsid', 'parent' => 'batchid', 'cname' => 'name', 'btname' => 'btname'),
			'conn' => 'exconn',
			'relation' => '`willingnesses` w, `students` s, `visits` v',
			'sqlprj' => 'w.`wlgsid`, w.`visitid`, w.`resume`, w.`status`, w.`approval`, w.`experience`, w.`name` as `wname`, w.`batch`, s.`stdid`, s.`username`, s.`name`, s.`email`, s.`rollno`, v.`vstatus`',
			'sqlcnd' => "where `wlgsid`=\${id} and w.`owner`=s.`owner` and v.`visitid`=w.`visitid` ",
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
		return array('wlgsid', 'batchid', 'btname', 'willingness', 'chain', 'pchain', 'admin', 'padmin');
	}
	
}

?>