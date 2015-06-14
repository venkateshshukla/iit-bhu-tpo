<?php 
require_once(SBSERVICE);

/**
 *	@class StageAdjacentWorkflow
 *	@desc Returns adjacent stage ID if within end time
 *
 *	@param stageid long int Stage ID [memory]
 * 	@param shlstid long int Shortlist ID [memory] optional default 0
 *	@param high boolean Is high [memory] optional default true
 *	@param keyid long int Usage Key ID [memory]
 *
 *	@return adjacent long int Adjacent Stage ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class StageAdjacentWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'stageid'),
			'optional' => array('shlstid' => 0, 'high' => true)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Adjacent Stage ID given successfully';
		$op = $memory['high'] ? '+' : '-';
		
		$workflow = array(
		array(
			'service' => 'transpera.reference.children.workflow',
			'input' => array('id' => 'shlstid')
		),
		array(
			'service' => 'cbcore.data.list.service',
			'args' => array('children'),
			'attr' => 'child',
			'default' => array(-1)
		),
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('stageid', 'list'),
			'conn' => 'cbslconn',
			'relation' => '`stages`',
			'sqlprj' => 'stageid',
			'sqlcnd' => "where `stageid` in \${list} and `stage`=((select `stage` from `stages` where `stageid`=\${stageid})".$op."1) and `end` >= now() and `start` <= now()",
			'escparam' => array('list'),
			'errormsg' => 'Beyond Stage Time / No Adjacent Stage'
		),
		array(
			'service' => 'cbcore.data.select.service',
			'args' => array('result'),
			'params' => array('result.0.stageid' => 'adjacent')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('adjacent');
	}
	
}

?>