<?php 
require_once(SBSERVICE);

/**
 *	@class SelectionAllWorkflow
 *	@desc Returns all selections information for owner
 *
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@return selections array Selections information [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class SelectionAllWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Selections information given successfully';
		
		$workflow = array(
			'service' => 'transpera.relation.select.workflow',
			'args' => array('keyid'),
			'conn' => 'cbslconn',
			'relation' => '`selections`',
			//'sqlprj' => 'e.`name` as `ename`, e.`home`, sl.`shlstid`, sl.`stageid`, st.`stage`, st.`name` as `stname`, st.`start`, st.`end`, st.`open`, (st.end > now()) as `ongoing`, sl.`status`',/*sl.`shlstid`=e.`shlstid` and sl.`stageid`=st.`stageid` and sl.*/
			'sqlcnd' => "where `owner`=\${keyid} order by st.`end` desc",
			'check' => false,
			'output' => array('result' => 'selections')
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('selections');
	}
	
}

?>