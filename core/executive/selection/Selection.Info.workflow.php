<?php 
require_once(SBSERVICE);

/**
 *	@class SelectionInfoWorkflow
 *	@desc Returns selection information by ID
 *
 *	@param selid/id long int Selection ID [memory]
 *	@param keyid long int Usage Key ID [memory] optional default false
 *	@param user string Key User [memory]
 *	@param shlstid long int Shortlist ID [memory] optional default 0
 *	@param shlstname/name string Shortlist name [memory] optional default ''
 *
 *	@return selection array Selection information [memory]
 *	@return shlstname string Shortlist name [memory]
 *	@return shlstid long int Shortlist ID [memory]
 *	@return admin integer Is admin [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SelectionInfoWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('selid'),
			'optional' => array('keyid' => false, 'user' => '', 'shlstname' => false, 'name' => '', 'shlstid' => false, 'id' => 0),
			'set' => array('id', 'name')
		); 
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['selid'] = $memory['selid'] ? $memory['selid'] : $memory['id'];
		
		$service = array(
			'service' => 'transpera.entity.info.workflow',
			'input' => array('id' => 'selid', 'parent' => 'shlstid', 'cname' => 'name', 'shlstname' => 'shlstname'),
			'conn' => 'exconn',
			'relation' => '`selections` l, `students` s, `grades` g',
			'sqlprj' => 'l.`selid`, l.`visitid`, l.`resume`, l.`stage`, l.`name` as `lname`, l.`batch`, s.`stdid`, s.`username`, s.`name`, s.`email`, s.`rollno`, g.`cgpa`, g.`sscx`, g.`hscxii`',
			'sqlcnd' => "where `selid`=\${id} and l.`owner`=s.`owner` and s.`grade`=g.`gradeid`",
			'errormsg' => 'Invalid Selection ID',
			'type' => 'selection',
			'successmsg' => 'Selection information given successfully',
			'output' => array('entity' => 'selection')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('selection', 'shlstname', 'shlstid', 'admin');
	}
	
}

?>