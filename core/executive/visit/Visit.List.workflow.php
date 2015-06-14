<?php 
require_once(SBSERVICE);

/**
 *	@class VisitListWorkflow
 *	@desc Returns all visits information in portal
 *
 *	@param keyid long int Usage Key ID [memory]
 *	@param portalid/id long int Portal ID [memory] optional default COMPANY_PORTAL_ID
 *	@param plname/name string Portal name [memory] optional default ''
 *	@param filter string Filter [memory] optional default false
 *	@param year string Year [memory] optional default false
 *
 *	@param pgsz long int Paging Size [memory] optional default 25
 *	@param pgno long int Paging Index [memory] optional default 0
 *	@param total long int Paging Total [memory] optional default false
 *	@param padmin boolean Is parent information needed [memory] optional default true
 *
 *	@return visits array Visits information [memory]
 *	@return portalid long int Portal ID [memory]
 *	@return plname string Portal Name [memory]
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
class VisitListWorkflow implements Service {
	
	/**
	 *	@interface Service
	**/
	public function input(){
		return array(
			'required' => array('keyid'),
			'optional' => array('user' => '', 'portalid' => false, 'id' => COMPANY_PORTAL_ID, 'plname' => false, 'name' => 'Campus Visit', 'pgsz' => 15, 'pgno' => 0, 'total' => false, 'padmin' => true, 'filter' => CURRENT_YEAR, 'year' => false, 'comuser' => false, 'current_year' => CURRENT_YEAR, 'next_year' => CURRENT_YEAR + 1, 'mail' => false),
			'set' => array('filter', 'year', 'comuser', 'id', 'name')
		);
	}
	
	/**
	 *	@interface Service
	**/
	public function run($memory){
		$memory['portalid'] = $memory['portalid'] ? $memory['portalid'] : $memory['id'];
		$memory['plname'] = $memory['plname'] ? $memory['plname'] : $memory['name'];
		$authcustom = false;
		
		$args = $esc = array();
		$qry = '';
		if($memory['filter']){
			if(in_array($memory['filter'], array('placement', 'internship', 'ppo'))){
				$memory['vtype'] = $memory['filter'];
				$qry .= "and `vtype`='\${vtype}'";
				array_push($args, 'vtype');
				array_push($esc, 'vtype');
				
				if($memory['year']){
					$qry .= "and `year`='\${year}'";
					array_push($args, 'year');
					array_push($esc, 'year');
				}
				
				if($memory['comuser']){
					$qry .= "and `comuser`='\${comuser}'";
					array_push($args, 'comuser');
					array_push($esc, 'comuser');
					$authcustom = true;
				}
			}
			elseif(in_array($memory['filter'], array('open', 'sent', 'interview', 'complete'))){
				switch($memory['filter']){
					case 'open':
						$memory['filter'] = 'Open for Willingness';
						break;
					case 'sent':
						$memory['filter'] = 'Willingness Sent';
						break;
					case 'interview':
						$memory['filter'] = 'Interview Shortlist';
						break;
					case 'complete':
					default:
						$memory['filter'] = 'Process Completed';
						break;
				}
				$memory['vtype'] = $memory['filter'];
				$qry .= "and `vstatus`='\${vtype}'";
				array_push($args, 'vtype');
				array_push($esc, 'vtype');
				$memory['pgsz'] = false;
				
				if($memory['year']){
					$qry .= "and `year`='\${year}'";
					array_push($args, 'year');
					array_push($esc, 'year');
				}
				
				if($memory['comuser']){
					$qry .= "and `comuser`='\${comuser}'";
					array_push($args, 'comuser');
					array_push($esc, 'comuser');
					$authcustom = true;
				}
			}
			elseif(is_numeric($memory['filter'])){
				$memory['vtype'] = false;
				$memory['tmp'] = $memory['year'];
				$memory['year'] = $memory['filter'];
				$qry = "and `year`='\${year}'";
				array_push($args, 'year');
				array_push($esc, 'year');
				
				if($memory['tmp'] && in_array($memory['tmp'], array('open', 'sent', 'interview', 'complete'))){
					switch($memory['tmp']){
						case 'open':
							$memory['tmp'] = 'Open for Willingness';
							break;
						case 'sent':
							$memory['tmp'] = 'Willingness Sent';
							break;
						case 'interview':
							$memory['tmp'] = 'Interview Shortlist';
							break;
						case 'complete':
						default:
							$memory['tmp'] = 'Process Completed';
							break;
					}
					$memory['vtype'] = $memory['tmp'];
					$qry .= "and `vstatus`='\${vtype}'";
					array_push($args, 'vtype');
					array_push($esc, 'vtype');
					$memory['vtype'] = $memory['tmp'];
					$memory['pgsz'] = false;
				}
				
				if($memory['tmp'] && in_array($memory['tmp'], array('placement', 'internship', 'ppo'))){
					$qry .= "and `vtype`='\${vtype}'";
					array_push($args, 'vtype');
					array_push($esc, 'vtype');
					$memory['vtype'] = $memory['tmp'];
				}
				
				if($memory['comuser'] && $memory['comuser'] != 'Schedule'){
					$qry .= "and `comuser`='\${comuser}'";
					array_push($args, 'comuser');
					array_push($esc, 'comuser');
					$authcustom = true;
				}
			}
			else {
				$memory['comuser'] = $memory['filter'];
				$memory['vtype'] = false;
				$qry = "and `comuser`='\${comuser}'";
				array_push($args, 'comuser');
				array_push($esc, 'comuser');
				$authcustom = true;
			}
		}
		else {
			$memory['vtype'] = false;
		}
		
		$comauth = array(
		array(
			'service' => 'transpera.relation.unique.workflow',
			'args' => array('keyid'),
			'conn' => 'exconn',
			'relation' => '`companies`',
			'sqlprj' => '`comid`',
			'sqlcnd' => "where `owner`=\${keyid} and `username`='".$memory['comuser']."'",
			'errormsg' => 'Unable to Authorize',
			'successmsg' => 'Company information given successfully'
		));
		
		$workflow = array(
		array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'portalid', 'pname' => 'plname'),
			'args' => $args,
			'conn' => 'exconn',
			'relation' => '`visits`',
			'type' => 'visit',
			'sqlprj' => '`visitid`, `vstname`, `files`, `shortlist`, `vtype`, `year`, `comid`, `comuser`, `remarks`, `issues`, `profile`, `vstatus`, `bpackage`, `ipackage`, `mpackage`, `visitdate`, UNIX_TIMESTAMP(`deadline`)*1000 as `deadline_ts`, `deadline`, UNIX_TIMESTAMP(`issuedl`)*1000 as `issuedl_ts`, `issuedl`, (select c.`name` from `companies` c where c.`comid`=`visits`.`comid`) as `comname`, `cer`, `che`, `civ`, `cse`, `eee`, `ece`, `mec`, `met`, `min`, `phe`, `apc`, `apm`, `app`, `bce`, `bme`, `mst`',
			'sqlcnd' => "where `visitid` in \${list} $qry order by `visitdate` desc, `vtype` desc, `visitid` desc",
			'escparam' => $esc,
			'successmsg' => 'Visits information given successfully',
			//'lsttrack' => true,
			'output' => array('entities' => 'visits'),
			'mapkey' => 'visitid',
			'mapname' => 'visit',
			'saction' => 'add',
			'authcustom' => $authcustom ? $comauth : false
		)/*,
		array(
			'service' => 'cbcore.data.list.service',
			'args' => array('visits'),
			'attr' => 'visitid',
			'mapname' => 'web',
			'default' => array(-1)
		),
		array(
			'service' => 'transpera.entity.list.workflow',
			'input' => array('id' => 'portalid', 'pname' => 'plname'),
			'args' => array('list'),
			'conn' => 'exconn',
			'relation' => '`cutoffs`',
			'type' => 'cutoff',
			'sqlprj' => '`ctfid`,  `dept`, `course`, `eligibility`, `margin`, `max`',
			'sqlcnd' => "where `ctfid` in \${list}",
			'successmsg' => 'Cutoffs information given successfully',
			//'lsttrack' => true,
			'output' => array('entities' => 'cutoffs'),
			'mapkey' => 'ctfid',
			'mapname' => 'cutoff',
			'saction' => 'add'
		)*/);
		
		$memory = Snowblozm::execute($workflow, $memory);
		if(!$memory['valid'])
			return $memory;
		
		$len = count($memory['visits']);
		for($i=0; $i<$len; $i++){
			$visit = $memory['visits'][$i];
			$service = array(
				'service' => 'executive.cutoff.list.workflow',
				'output' => array('admin' => 'ctfadmin', 'padmin' => 'admin'),
				'keyid' => $memory['keyid'],
				'user' => $memory['user'],
				'visitid' => $visit['visit']['visitid'],
				'vstname' => $visit['visit']['vstname'],
				'pgsz' => 3,
				'pgno' => 0,
				'total' => $visit['chain']['count'],
				'chpgsz' => 3,
				'chpgno' => 0,
				'chtotal' => $visit['chain']['count']
			);
			$memory['visits'][$i] = Snowblozm::run($service, $visit);
		}
		
		if($memory['valid'] && $memory['mail']){
			$visits = $memory['visits'];
			
			$memory['keyid'] = TPO_KEY;
			$memory['queid'] = FORM_ID;
			$memory['msg'] = 'Mail Sent Successfully';
	
			$mailto = Snowblozm::get('form_mailto');
			$today = date("D M j Y"); 
			$memory['mailto'] = $memory['mail'] == 'intern' ? $mailto['digest_intern'] : $mailto['digest'];
			$memory['subject'] = '[T&P] [Urgent] Opportunities Digest for '.$today;
			
			$memory['body'] = 'Dear All,

This is to inform you that at present we have the following companies open for willingness for various branches. 
Please ensure that the eligible interested student gives willingness for the respective companies:
';
			
			$i = 0;
			foreach($visits as $v){
				if(($memory['mail'] == 'intern' && $v['visit']['vtype'] != 'internship') || ($memory['mail'] != 'intern' && $v['visit']['vtype'] != 'placement')) 
					continue;
				$i++;
				$visit = $v['visit'];
				$memory['body'] .= '
'.$i.'. '.$visit['comname'];
				if($visit['profile']) $memory['body'] .= ' [ '.$visit['profile'].' ]';
				if($v['cutoffs']){
					foreach($v['cutoffs'] as $c){
						$cutoff = $c['cutoff'];
						$memory['body'] .= '
	CGPA > '.$cutoff['eligibility'].' 
	Department(s): '.$cutoff['dept'].' 
	Course(s): '.$cutoff['course'].'
';
					}
				}
				else {
					$memory['body'] .= '
	Open to All
';
				}
			}
			
			$memory['body'] .= '
			
--
T&P Portal Online Forms

Training and Placement Cell
IIT (BHU) Varanasi - 221005
http://iitbhu.ac.in/tpo/
';
			
			$workflow = array(
			array(
				'service' => 'queue.mail.add.workflow',
				'input' => array('to' => 'mailto'),
			), 
			array(
				'service' => 'queue.mail.send.workflow'
			));

			return Snowblozm::execute($workflow, $memory);
		}
		else
			return $memory;
	}
	
	/**
	 *	@interface Service
	**/
	public function output(){
		return array('visits', 'id', 'portalid', 'plname', 'admin', 'padmin', 'pchain', 'total', 'pgno', 'pgsz', 'vtype', 'comuser', 'year', 'current_year', 'next_year');
	}
	
}

?>