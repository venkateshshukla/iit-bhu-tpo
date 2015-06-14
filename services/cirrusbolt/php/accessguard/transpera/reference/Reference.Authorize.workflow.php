<?php 
require_once(SBSERVICE);

/**
 *	@class ReferenceAuthorizeWorkflow
 *	@desc Manages authorization of existing reference 
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param id long int Reference ID [memory]
 *
 *	@param action string Action to authorize [memory] optional default 'edit'
 *	@param acstate string State to authorize chain [memory] optional default true (false= All)
 *	@param astate string State to authorize member [memory] optional default true (false= All)
 *	@param iaction string Action to authorize inherit [memory] optional default 'edit'
 *	@param aistate string State to authorize inherit [memory] optional default true (false= All)
 *	@param init boolean init flag [memory] optional default true
 *	@param self boolean self flag [memory] optional default false
 *
 *	@param cache boolean Is cacheable [memory] optional default true
 *	@param expiry int Cache expiry [memory] optional default 150
 *
 *	@param admin boolean Is return admin flag [memory] optional default false
 *	@param authinh integer Check inherit [memory] optional default 1
 *	@param autherror string Error msg [memory] optional default 'Unable to Authorize'
 *	@param authcustom array Custom Check Workflow [memory] optional default false 
 *
 *	@return masterkey long int Master key ID [memory]
 *	@return admin boolean Is admin [memory]
 *	@return level integer Level [memory]
 *	@return authorize string Authorization Control [memory]
 *	@return state string State [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ReferenceAuthorizeWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid', 'id'),
			'optional' => array(
				'acstate' => true, 
				'action' => 'edit', 
				'astate' => true, 
				'iaction' => 'edit', 
				'aistate' => true, 
				'admin' => false, 
				'init' => true,
				'self' => false,
				'authinh' => 1,
				'autherror' => 'Unable to Authorize',
				'authcustom' => false,
				'cache' => true,
				'expiry' => 150
			)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$cache = $memory['cache'];

		if($cache){
			$poolkey = 'REFERENCE_AUTHORIZE_'.json_encode($memory);
			$pool = Snowblozm::run(array(
				'service' => 'pool.lite.get.service',
				'key' => $poolkey
			), array());
		}
		
		if($cache && $pool['valid']){
			$memory = $pool['data'];
		} 
		else {
			
			$memory['msg'] = 'Reference authorized successfully';
			
			if($memory['keyid'] === false){
				$memory['masterkey'] = $memory['admin'] = $memory['level'] = 0;
				$memory['authorize'] = 'edit:add:remove:list';
				$memory['state'] = 'A';
				$memory['valid'] = true;
				$memory['status'] = 200;
				$memory['details'] = 'Successfully executed';
				return $memory;
			}
			
			$service = array(
				'service' => 'guard.chain.authorize.workflow',
				'input' => array(
					'chainid' => 'id', 
					'cstate' => 'acstate', 
					'mstate' => 'astate', 
					'istate' => 'aistate',
					'rucache' => 'arucache',
					'ruexpiry' => 'aruexpiry',
					'srucache' => 'srucache',
					'sruexpiry' => 'asruexpiry',
					'inherit' => 'authinh',
					'errormsg' => 'autherror',
					'custom' => 'authcustom'
				)
			);
		
			$memory = Snowblozm::run($service, $memory);
			if($cache){
				Snowblozm::run(array(
					'service' => 'pool.lite.save.service',
					'key' => $poolkey,
					'data' => $memory
				), array());
			}
		}
		
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('masterkey', 'grroot', 'admin', 'level', 'grlevel', 'authorize', 'state');
	}
	
}

?>