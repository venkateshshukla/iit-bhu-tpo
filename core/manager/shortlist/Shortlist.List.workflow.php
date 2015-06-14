<?php 
require_once(SBSERVICE);

/**
 *	@class ShortlistListWorkflow
 *	@desc Returns all shortlist information
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param shlstid/id long int Shortlist ID [memory]
 *	@param shlstname/name string Shortlist name [memory] optional default ''
 *
 *	@param pgsz long int Paging Size [memory] optional default 25
 *	@param pgno long int Paging Index [memory] optional default 0
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return stages array Stages information [memory]
 *	@return selections array Selections information [memory]
 *	@return shlstid long int Shortlist ID [memory]
 *	@return shlstname string Shortlist Name [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return pgsz long int Paging Size [memory]
 *	@return pgno long int Paging Index [memory]
 *	@return total long int Paging Total [memory]
 *	@return sladmin integer Is admin [memory]
 *	@return slpadmin integer Is parent admin [memory]
 *	@return slpchain array Parent chain information [memory]
 *	@return slpgsz long int Paging Size [memory]
 *	@return slpgno long int Paging Index [memory]
 *	@return sltotal long int Paging Total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class ShortlistListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'shlstid' => false, 'id' => 0, 'shlstname' => false, 'name' => '', 'pgsz' => false, 'pgno' => 0, 'total' => false, 'padmin' => true, 'stage' => false),
			'set' => array('id', 'name', 'stage')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['shlstid'] = $memory['shlstid'] ? $memory['shlstid'] : $memory['id'];
		$memory['shlstname'] = $memory['shlstname'] ? $memory['shlstname'] : $memory['name'];
		
		$workflow = array(
		array(
			'service' => 'shortlist.stage.list.workflow',
		),
		array(
			'service' => 'shortlist.selection.list.workflow',
			'output' => array(
				'admin' => 'sladmin', 
				'padmin' => 'slpadmin', 
				'pchain' => 'slpchain', 
				'total' => 'sltotal', 
				'pgno' => 'slpgno', 
				'pgsz' => 'slpgsz'
			)
		),
		array(
			'service' => 'cbcore.data.list.service',
			'args' => array('selections'),
			'attr' => 'refer',
			'mapname' => 'selection',
			'default' => array(-1)
		),
		array(
			'service' => 'transpera.relation.select.workflow',
			'args' => array('list'),
			'conn' => 'exconn',
			'relation' => '`students` s, `grades` g',
			'sqlprj' => 's.`stdid`, s.`username`, s.`name`, s.`email`, s.`rollno`, s.`resume`, s.`home`, s.`interests`, g.`cgpa`, g.`sscx`, g.`hscxii`',
			'sqlcnd' => "where `stdid` in \${list} and s.`grade`=g.`gradeid`",
			'escparam' => array('list'),
			'check' => false,
			'output' => array('result' => 'students')
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('stages', 'selections', 'students', 'shlstid', 'shlstname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz', 'sladmin', 'slpadmin', 'slpchain', 'sltotal', 'slpgno', 'slpgsz');
	}
	
}

?>