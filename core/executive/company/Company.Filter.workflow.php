<?php 
require_once(SBSERVICE);

/**
 *	@class CompanyListWorkflow
 *	@desc Returns all companies information in portal container
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param portalid/id long int Portal ID [memory] optional default COMPANY_PORTAL_ID
 *	@param plname/name string Portal name [memory] optional default ''
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@param filter string Filter [memory] optional default false
 *	@param year string Year [memory] optional default false
 *	@param course string course [memory] optional default false
 *
 *	@return companies array Companys information [memory]
 *	@return portalid long int Portal ID [memory]
 *	@return plname string Portal name [memory]
 *	@return admin integer Is admin [memory]
 *	@return padmin integer Is parent admin [memory]
 *	@return pchain array Parent chain information [memory]
 *	@return pgsz long int Paging Size [memory]
 *	@return pgno long int Paging Index [memory]
 *	@return total long int Paging Total [memory]
 *
 *	@author Vibhaj Rajan <vibhaj8@gmail.com>
 *
**/
class CompanyListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('portalid' => false, 'id' => COMPANY_PORTAL_ID, 'plname' => false, 'name' => '', 'pgsz' => 25, 'pgno' => 0, 'total' => false),
			'set' => array('filter', 'year', 'course', 'id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['portalid'] = $memory['portalid'] ? $memory['portalid'] : $memory['id'];
		$memory['plname'] = $memory['plname'] ? $memory['plname'] : $memory['name'];
		$memory['dept'] = false;
		
		$args = $esc = array();
		if($memory['filter']){
			if(is_numeric($memory['filter'])){
				$memory['year'] = $memory['filter'];
				$qry = "and `year`=\${year}";
				array_push($args, 'year');
				
				if($memory['course']){
					$qry .= " and `course`='\${course}'";
					array_push($args, 'course');
					array_push($esc, 'course');
				}
			}
			elseif(in_array($memory['filter'], array('cer', 'che', 'civ', 'cse', 'eee', 'ece', 'mec', 'met', 'min', 'phe', 'apc', 'apm', 'app', 'bce', 'bme', 'mst'))){
				$memory['dept'] = $memory['filter'];
				$qry = "and `dept`='\${dept}'";
				array_push($args, 'dept');
				array_push($esc, 'dept');
				
				if($memory['year']){
					$qry .= " and `year`=\${year}";
					array_push($args, 'year');
					
					if($memory['course']){
						$qry .= " and `course`='\${course}'";
						array_push($args, 'course');
						array_push($esc, 'course');
					}
				}
			}
			elseif(in_array($memory['filter'], array('btech', 'idd', 'mtech'))){
				$memory['course'] = $memory['filter'];
				$qry = "and `course`='\${course}'";
				array_push($args, 'course');
				array_push($esc, 'course');
			}
		}
		else {
			$memory['dept'] = false;
		}
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'portalid', 'pname' => 'plname'),
			'args' => $args,
			'conn' => 'exconn',
			'relation' => '`batches`',
			'type' => 'batch',
			'sqlprj' => '`portalid`, `plname`, `resumes`, `notes`, `dept`, `course`, `year`',
			'sqlcnd' => "where `portalid` in \${list} $qry order by `year` desc, `dept` asc, `course` asc",
			'escparam' => $esc,
			'successmsg' => 'Batches information given successfully',
			'lsttrack' => false,
			'output' => array('entities' => 'batches'),
			'ismap' => false,
			'saction' => 'add'
			
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'portalid', 'pname' => 'plname'),
			'conn' => 'exconn',
			'relation' => '`companies`',
			'sqlprj' => '`comid`, `username`, `name`, `resume`, `home`, `interests`',
			'sqlcnd' => "where `comid` in \${list} and `ustatus`<>'N' order by `cgpa` desc",
			'type' => 'company',
			'successmsg' => 'Companys information given successfully',
			'output' => array('entities' => 'companies'),
			'mapkey' => 'comid',
			'mapname' => 'company'
		));
		
		return Snowblozm::execute($workflow, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('companies', 'id', 'portalid', 'plname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz', 'dept', 'year', 'course');
	}
	
}

?>