<?php 
require_once(SBSERVICE);

/**
 *	@class CompanyListWorkflow
 *	@desc Returns all companies information in portal container
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param portalid/id long int Portal ID [memory] optional default COMPANY_PORTAL_ID
 *	@param plname/name string Portal name [memory] optional default 'IT BHU Recruiters'
 *	@param pgsz long int Paging Size [memory] optional default false
 *	@param pgno long int Paging Index [memory] optional default 1
 *	@param total long int Paging Total [memory] optional default false
 *
 *	@param scope string Scope [memory] optional default false
 *
 *	@return companies array Companies information [memory]
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
			'optional' => array('portalid' => false, 'id' => COMPANY_PORTAL_ID, 'plname' => false, 'name' => 'Company Account', 'pgsz' => false, 'pgno' => 0, 'total' => false, 'padmin' => true, 'scope' => false),
			'set' => array('id', 'name', 'scope')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['portalid'] = $memory['portalid'] ? $memory['portalid'] : ( $memory['id'] ? $memory['id'] : COMPANY_PORTAL_ID );
		$memory['plname'] = $memory['plname'] ? $memory['plname'] : $memory['name'];
		
		$args = $esc = array();
		$qry = '';
		if($memory['scope']){
			$qry = "and `scope` like '%\${scope}%'";
			array_push($args, 'scope');
			array_push($esc, 'scope');
		}
		
		$service = array(
			'service' => 'transpera.entity.list.workflow',
			'args' => $args,
			'input' => array('id' => 'portalid', 'pname' => 'plname'),
			'conn' => 'exconn',
			'relation' => '`companies`',
			'sqlprj' => '`comid`, `username`, `name`, `site`, `folder`, `notes`, `scope`',
			'sqlcnd' => "where `comid` in \${list} $qry order by `name`",
			'type' => 'person',
			'successmsg' => 'Companys information given successfully',
			'escparam' => $esc,
			'output' => array('entities' => 'companies'),
			'mapkey' => 'comid',
			'mapname' => 'company'
		);
		
		return Snowblozm::run($service, $memory);
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('companies', 'id', 'portalid', 'plname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz');
	}
	
}

?>