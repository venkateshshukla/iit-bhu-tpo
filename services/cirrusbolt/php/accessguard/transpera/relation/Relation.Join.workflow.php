<?php 
require_once(SBSERVICE);

/**
 *	@class RelationJoinWorkflow
 *	@desc Executes SELECT query on JOIN relations returning all results in resultset
 *
 *	@param relations array Relations configurations [memory]
 *	@param sqlcnd string SQL condition [memory]
 *	@param sqlprj string SQL projection [memory] optional default *
 *
 *	@param relation string Relation name [config]
 *	@param alias string Relation alias [config] optional default ''
 *	@param sqlcnd string SQL condition [config] optional default ''
 *	@param sqlprj string SQL projection [config] optional default *
 *
 *	@param args array Query parameters [args]
 *	@param escparam array Escape parameters [memory] optional default array()
 *	@param check boolean Is validate [memory] optional default true
 *	@param errormsg string Error message [memory] optional default 'Error in Database'
 *
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@param mapkey string Map Key [memory] optional default 0
 *	@param mapname string Map Name [memory] optional default 'data'
 *	@param ismap boolean Is map [memory] optional default true
 *
 *	@param cache boolean Is cacheable [memory] optional default true
 *	@param expiry int Cache expiry [memory] optional default 85
 *
 *	@param conn array DataService instance configuration key [memory]
 *
 *	@return result array Resultset [memory] 
 *	@return total long int Paging total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class RelationJoinWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('conn', 'relations', 'sqlcnd'),
			'optional' => array(
				'sqlprj' => '*', 
				'escparam' => array(), 
				'errormsg' => 'Error in Database', 
				'check' => true, 
				'pgsz' => false, 
				'pgno' => 0, 
				'total' => false, 
				'mapkey' => 0,
				'mapname' => 'data',
				'ismap' => true,
				'cache' => false,
				'expiry' => 85
			)
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$cache = $memory['cache'];

		if($cache){
			$poolkey = 'RELATION_JOIN_'.json_encode($memory);
			$pool = Snowblozm::run(array(
				'service' => 'pool.lite.get.service',
				'key' => $poolkey
			), array());
		}
		
		if($cache && $pool['valid']){
			$memory = $pool['data'];
		} 
		else {
		
			$relations = $memory['relations'];
			$relarray = array();
			foreach($relations as $config){
				$prj = isset($config['sqlprj']) ? $config['sqlprj'] : '*';
				$cnd = isset($config['sqlcnd']) ? $config['sqlcnd'] : '';
				$alias = isset($config['alias']) ? $config['alias'] : '';
				$rel = $config['relation'];
				array_push($relation, "(select $prj from $rel $cnd) $alias");
			}
			$relation = implode(',', $relarray);
			
			$pgsz = $memory['pgsz'];
			$limit = '';
			
			if($pgsz){
				if(!$memory['total']){
					$service = array(
						'service' => 'rdbms.query.execute.workflow',
						'args' => $memory['args'],
						'output' => array('sqlresult' => 'result'),
						'query' => 'select count(*) as total from '.$relation.' '.$memory['sqlcnd'].';',
						'count' => 0,
						'not' => false
					);
					
					$memory = Snowblozm::run($service, $memory);
					if(!$memory['valid'])
						return $memory;
					
					$memory['total'] = $memory['result'][0]['total'];
				}
				
				$limit = ' limit '.($pgsz*$memory['pgno']).','.$pgsz;
			}
			
			$workflow = array(
			array(
				'service' => 'rdbms.query.execute.workflow',
				'args' => $memory['args'],
				'output' => array('sqlresult' => 'result'),
				'query' => 'select '.$memory['sqlprj'].' from '.$relation.' '.$memory['sqlcnd'].$limit.';',
				'count' => 0,
				'not' => false
			));
			
			if($memory['ismap']){
			array_push($workflow,
				array(
					'service' => 'cbcore.data.map.service',
					'input' => array('data' => 'result')
				));
			}
			
			$memory = Snowblozm::execute($workflow, $memory);
		
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
		return array('result', 'total');
	}
	
}

?>