<?php 
require_once(SBSERVICE);

/**
 *	@class KeyIdentifyWorkflow
 *	@desc Identifies key from email and returns hash of it with challenge sent 
 *	@condition identifies only if (user) is set and (key, keyid) not set else returns what is sent
 *
 *	@param user string Email [memory] optional default false
 *	@param context string Application context for email [memory] optional default false
 *	@param challenge string Challenge to be used while hashing [memory] optional default false
 *	@param key string Key value hash already generated previously [memory] optional default false
 *	@param keyid string Key ID returned previously [memory] optional default false
 *
 *	@return key string Key value hash [memory]
 *	@return keyid long int Key ID [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class KeyIdentifyWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'optional' => array('challenge' => false, 'user' => false, 'key' => false, 'keyid' => false, 'context' => false, 'silent' => false)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['user'] = $memory['user'] ? $memory['user'] : false;
		
		if($memory['user'] !==false && $memory['key'] === false && $memory['keyid'] === false){
			if(!$memory['context']){
				$memory['valid'] = false;
				$memory['msg'] = 'Invalid Context';
				$memory['status'] = 515;
				$memory['details'] = 'Invalid context : '.$memory['context'].' @guard.key.identify service';
				return $memory;
			}
		
			$memory['msg'] = 'Key identified successfully';
			$args = array('user', 'context');
			$qry = $memory['challenge'] ? "MD5(concat(`keyvalue`,'\${challenge}'))" : "`keyid`";
			if($memory['challenge'])
				array_push($args, 'challenge');
			
			$workflow = array(
			array(
				'service' => 'transpera.relation.unique.workflow',
				'args' => $args,
				'conn' => 'cbconn',
				'relation' => '`keys`',
				'sqlprj' => "keyid, $qry as `key`",
				'sqlcnd' => "where `user`='\${user}' and `context` like '%\${context}%'",
				'escparam' => $args,
				'errormsg' => 'Unable to identify key for user',
				'errstatus' => 515,
				'cache' => false
			),
			array(
				'service' => 'cbcore.data.select.service',
				'args' => array('result'),
				'params' => array('result.0.keyid' => 'keyid', 'result.0.key' => 'key')
			));
			
			$memory = Snowblozm::execute($workflow, $memory);
			
			if(!$memory['valid'] && $memory['silent']){
				$memory['keyid'] = -1;
				$memory['key'] = 'krishnakripa';
				$memory['valid'] = true;
			}
		}
		else {
			$memory['valid'] = true;
		}
		
		return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('keyid', 'key');
	}
	
}

?>