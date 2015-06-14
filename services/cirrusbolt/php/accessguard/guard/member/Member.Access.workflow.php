<?php 
require_once(SBSERVICE);

/**
 *	@class MemberAccessWorkflow
 *	@desc Returns chain IDs in chain for member key ID in type
 *
 *	@param keyid long int Key ID [memory]
 *	@param state string State [memory] optional default false (true= Not '0')
 *	@param type string Type name [memory] optional default 'general'
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@return result array Chain IDs [memory]
 *	@return total long int Paging total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class MemberAccessWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('type' => 'general', 'state' => false, 'pgsz' => false, 'pgno' => 0, 'total' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['msg'] = 'Access chains returned successfully';
		$last = '';
		$escparam = array('type');
		
		if($memory['state']){
			$last = " and `state`='\${state}' ";
			array_push($escparam, 'state');
		}
		
		$service = array(
			'service' => 'transpera.relation.select.workflow',
			'args' => array('keyid', 'type', 'state'),
			'conn' => 'cbconn',
			'relation' => '`members`',
			'sqlprj' => '`chainid`',
			'sqlcnd' => "where `type`='\${type}' and `keyid`=\${keyid} $last",
			'escparam' => $escparam,
			'errormsg' => 'No Access'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('result', 'total');
	}
	
}

?>